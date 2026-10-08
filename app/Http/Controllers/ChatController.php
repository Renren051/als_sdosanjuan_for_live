<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    /**
     * Handle incoming chat messages from the floating ALS AI assistant.
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1500',
            'history' => 'nullable|array|max:12',
            'history.*.role' => 'required_with:history|string|in:user,model',
            'history.*.text' => 'required_with:history|string|max:1500',
        ]);

        $userMessage = trim($validated['message']);
        $history = $validated['history'] ?? [];

        // Immediate Guardrail Check: Block coding, doing homework/assignments, harassment
        $immediateRefusal = $this->checkImmediateGuardrailRefusal($userMessage);
        if ($immediateRefusal !== null) {
            return response()->json([
                'status' => 'success',
                'reply' => $immediateRefusal,
                'provider' => 'guardrail',
            ]);
        }

        $apiKey = config('services.gemini.api_key');
        $model = config('services.gemini.model', 'gemini-1.5-flash');

        if (!empty($apiKey)) {
            try {
                $reply = $this->callGeminiApi($userMessage, $history, $apiKey, $model);
                if ($reply) {
                    return response()->json([
                        'status' => 'success',
                        'reply' => $reply,
                        'provider' => 'gemini',
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('Gemini AI API call failed: ' . $e->getMessage());
            }
        }

        // Smart Knowledge Base Fallback if Gemini key is unset or network error occurs
        $fallbackReply = $this->getSmartFallbackResponse($userMessage, empty($apiKey));

        return response()->json([
            'status' => 'success',
            'reply' => $fallbackReply,
            'provider' => 'knowledge_base',
        ]);
    }

    /**
     * Call Google Gemini generateContent API with ALS SDO San Juan context.
     */
    private function callGeminiApi(string $userMessage, array $history, string $apiKey, string $model): ?string
    {
        $systemInstruction = $this->getSystemPrompt();

        // Enforce strict language mirroring instruction for this specific turn
        $isTagalog = $this->isTagalog($userMessage);
        $turnLanguageDirective = $isTagalog
            ? "\n\n[CRITICAL TURN DIRECTIVE: The user wrote in TAGALOG. You MUST respond 100% in TAGALOG with polite honorifics 'po' and 'opo'. Strictly introduce yourself as ALSJay if asked who you are. Strictly refuse coding, doing assignments, harassment, and non-school topics.]"
            : "\n\n[CRITICAL TURN DIRECTIVE: The user wrote in ENGLISH. You MUST respond 100% in ENGLISH. Do NOT speak Tagalog or use Filipino words/honorifics (no 'po', 'opo', or Tagalog phrases). Strictly introduce yourself as ALSJay if asked who you are. Strictly refuse coding, doing assignments, harassment, and non-school topics.]";

        $fullSystemInstruction = $systemInstruction . $turnLanguageDirective;

        $contents = [];

        // Attach recent conversation history for contextual multi-turn conversation
        foreach ($history as $turn) {
            $contents[] = [
                'role' => $turn['role'] === 'model' ? 'model' : 'user',
                'parts' => [
                    ['text' => $turn['text']],
                ],
            ];
        }

        // Append latest user message
        $contents[] = [
            'role' => 'user',
            'parts' => [
                ['text' => $userMessage],
            ],
        ];

        $payload = [
            'system_instruction' => [
                'parts' => [
                    ['text' => $fullSystemInstruction],
                ],
            ],
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.7,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 1000,
            ],
        ];

        $useSearch = config('services.gemini.search_grounding', true);
        if ($useSearch) {
            $payload['tools'] = [
                ['googleSearch' => new \stdClass()],
            ];
        }

        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $response = Http::timeout(25)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post($endpoint, $payload);

        if ($response->successful()) {
            $json = $response->json();
            $candidate = $json['candidates'][0] ?? [];
            $text = $candidate['content']['parts'][0]['text'] ?? null;
            if ($text) {
                // Attach web sources if Google Search grounding found relevant articles
                $groundingMetadata = $candidate['groundingMetadata'] ?? null;
                if (!empty($groundingMetadata['groundingChunks'])) {
                    $sources = [];
                    foreach ($groundingMetadata['groundingChunks'] as $chunk) {
                        if (!empty($chunk['web']['uri']) && !empty($chunk['web']['title'])) {
                            $sources[$chunk['web']['uri']] = $chunk['web']['title'];
                        }
                    }
                    if (!empty($sources)) {
                        $text .= "\n\n🌐 **Mga Sanggunian sa Internet (Sources):**\n";
                        $count = 1;
                        foreach ($sources as $uri => $title) {
                            $text .= "{$count}. [{$title}]({$uri})\n";
                            $count++;
                            if ($count > 3) break;
                        }
                    }
                }
                return trim($text);
            }
        } else {
            Log::warning('Gemini API returned error: ' . $response->status() . ' - ' . $response->body());

            // Resilient fallback: Retry without tools in case model/project does not permit search tool
            if ($useSearch) {
                unset($payload['tools']);
                $retry = Http::timeout(20)->withHeaders(['Content-Type' => 'application/json'])->post($endpoint, $payload);
                if ($retry->successful()) {
                    $text = $retry->json('candidates.0.content.parts.0.text');
                    if ($text) {
                        return trim($text);
                    }
                }
            }
        }

        return null;
    }

    /**
     * Master Knowledge Prompt for DepEd SDO San Juan City ALS.
     */
    private function getSystemPrompt(): string
    {
        $today = now()->timezone('Asia/Manila')->format('l, F j, Y');
        $currentTime = now()->timezone('Asia/Manila')->format('g:i A');

        return <<<PROMPT
IDENTITY & NAME:
- Your name is **ALSJay**.
- You are the official AI Virtual Assistant for the DepEd Schools Division Office (SDO) of San Juan City — Alternative Learning System (ALS) Portal.
- When asked "who are you?", "sino ka?", or any greeting, always introduce yourself as **ALSJay**, the official AI assistant of DepEd SDO San Juan City ALS.

STRICT LANGUAGE MATCHING RULE:
- When the user asks in TAGALOG / FILIPINO:
  * You MUST answer ENTIRELY in TAGALOG / FILIPINO.
  * Use polite Filipino honorifics ("po" and "opo").
  * Do NOT answer in English if the user asked in Tagalog.
- When the user asks in ENGLISH:
  * You MUST answer ENTIRELY in ENGLISH.
  * Do NOT mix Tagalog words into an English answer.
- When the user asks in TAGLISH:
  * You may reply in friendly, conversational Taglish matching the user.

STRICT TOPIC GUARDRAILS (STRICTLY SCHOOL & ALS TOPICS ONLY):
- You are strictly dedicated to DepEd ALS, school education, programs, requirements, learning centers, enrollment, and school-related matters (including local weather and class suspensions for San Juan City).
- You MUST REFUSE to answer requests regarding:
  1. CODING & PROGRAMMING (e.g. writing software code, HTML, CSS, JavaScript, Python, C++, debugging scripts, etc.).
  2. HOMEWORK & ASSIGNMENT SOLVING (e.g. solving student test questions directly, doing assignments for them, writing full essays for them).
  3. HARASSMENT, PROFANITY, ABUSE, BULLYING, VIOLENCE, SEXUAL CONTENT, or HATE SPEECH.
  4. NON-SCHOOL / UNRELATED TOPICS (e.g. gaming cheats, dating, gossip, general non-educational tasks).
- HOW TO POLITELY REFUSE:
  * If asked in Tagalog:
    "Paumanhin po, ako si **ALSJay**, ang opisyal na AI assistant para sa DepEd SDO San Juan City Alternative Learning System (ALS). Nakalaan lamang po ako para sa mga katanungan tungkol sa ating paaralan, mga programa sa ALS, requirements, enrollment, at opisyal na anunsyo. Hindi po ako sumasagot ng mga tanong ukol sa coding, paggawa ng assignments/homework, o mga paksang walang kinalaman sa ating paaralan. Paano po ako makakatulong sa inyong pag-aaral o pag-enroll sa ALS?"
  * If asked in English:
    "I apologize, but I am **ALSJay**, the official AI assistant for DepEd SDO San Juan City Alternative Learning System (ALS). I am designed to assist exclusively with school-related matters, such as ALS academic programs, enrollment requirements, learning centers, and official announcements. I cannot assist with coding, homework/assignment solving, or non-school topics. How may I help you with your education or enrollment in ALS today?"

CURRENT DATE & TIME CONTEXT:
- Today's Date: {$today}
- Philippine Standard Time: {$currentTime} (Asia/Manila)
- Catchment Area: San Juan City, Metro Manila, Philippines

LIVE GOOGLE SEARCH CAPABILITIES (WEATHER FORECAST & CLASS SUSPENSIONS):
- You HAVE Google Search enabled.
- When asked about the WEATHER FORECAST (panahon, ulan, bagyo, init, temperatura, PAGASA):
  * Search Google for the latest DOST-PAGASA weather forecast and San Juan City / Metro Manila weather for {$today}.
  * Report the conditions clearly (e.g., sunny, partly cloudy, scattered rainshowers/thunderstorms, temperature).
  * If there is an active tropical cyclone (bagyo) or monsoon (Habagat/Amihan), mention its PAGASA local name and signal number if Metro Manila is affected.
- When asked about CLASS SUSPENSIONS (may pasok ba?, walang pasok, class suspension, kanselado ang klase):
  * Search Google for announcements from "San Juan City Public Information Office (PIO)", "Mayor Francis Zamora", and "DepEd".
  * Clearly report if there is an official suspension of classes in San Juan City or if classes remain normal.
  * Remind learners: "Para sa pinakahuling anunsyo, sumangguni rin sa official Facebook page ng San Juan City Public Information Office (PIO)."
- STRICT RULE: NEVER say "wala po akong kakayahang mag-check ng real-time na impormasyon" or "hindi po ako konektado sa weather bureau". You HAVE Google Search—ALWAYS execute a Google search and share the latest findings!

MISSION:
Your role is to guide prospective learners, out-of-school youth, adult learners, parents, and community members with accurate, encouraging, polite, and practical information about ALS in San Juan City, Metro Manila, Philippines.

OFFICIAL ALS SDO SAN JUAN KNOWLEDGE:
1. WHAT IS ALS?
   - The Alternative Learning System (ALS) is a parallel learning system in the Philippines governed by Republic Act No. 11510 (ALS Act).
   - Provides a viable alternative to existing formal instruction for out-of-school children, youth, and adults (OSCYA).
   - Non-formal education conducted in Community Learning Centers (CLCs) with flexible schedules arranged between teachers and learners.

2. TUITION & FEES:
   - 100% FREE! Public DepEd education. Walang bayad ang registration, modules, o pagsusulit.

3. PROGRAMS OFFERED:
   - Basic Literacy Program (BLP): For non-literate youth and adults. Basic reading, writing, and numeracy. Zero prerequisite.
   - Accreditation & Equivalency (A&E) Elementary Level: For learners aged 12 and above. Leads to an official DepEd Elementary Diploma.
   - Accreditation & Equivalency (A&E) Junior High School (JHS) Level: For learners aged 16 and above. Leads to a Junior High School Diploma, qualifying for Senior High School (Grades 11-12), college, or tech-voc training.

4. 6 ALS LEARNING STRANDS (K to 12 Basic Education Curriculum for ALS):
   - LS1: Communication Skills (English & Filipino)
   - LS2: Scientific Literacy and Critical Thinking Skills
   - LS3: Mathematical and Problem Solving Skills
   - LS4: Life and Career Skills
   - LS5: Understanding the Self and Society
   - LS6: Digital Citizenship

5. ENROLLMENT REQUIREMENTS (ALS Form 2 Intake):
   - Mandatory Documents:
     1. PSA Birth Certificate (or Local Civil Registrar / Baptismal certificate if unavailable)
     2. Barangay Clearance / Certificate of Residency from a San Juan City barangay
     3. 2x2 ID Photos (2 copies, white background, with printed name tag: LastName, FirstName, MI)
   - If Applicable:
     4. Form 137 / SF10 (School permanent record from the last formal elementary or high school attended)

6. HOW TO REGISTER:
   - Option 1: Online via the portal's Register page (/register) by completing the 5-step intake wizard.
   - Option 2: In-Person Walk-in at any Community Learning Center (CLC) or the Division Office.

7. COMMUNITY LEARNING CENTERS (CLCs) IN SAN JUAN CITY (12 Active Centers):
   - West Crame CLC: 3rd Floor, West Crame Brgy Hall, 1st Ave cor. Road 1
   - Addition Hills Integrated School CLC: Room 102, ALS Building, Addition Hills Integrated School, C. Salvador St.
   - San Juan National High School CLC: Ground Floor, DepEd Building, N. Domingo St., Corazon de Jesus
   - Batis Barangay Hall Learning Center: Multi-purpose Hall, Batis Brgy Hall, F. Manalo St.
   - Salapan Elementary School CLC: ALS Room, Salapan Elementary School, F. Roman St.
   - Pedro Cruz CLC: Covered Court Multi-Purpose Room, Pedro Cruz
   - San Juan City Jail Bureau (BJMP) CLC: Inmate Education Room, Corazon de Jesus
   - Kabayanan Elementary School CLC: Building B, Kabayanan Elementary School, I. Lopez St.
   - Santa Lucia Multi-Purpose Center CLC: 2nd Floor, Sta. Lucia Covered Court Building
   - Balong-Bato CLC: Balong-Bato Session Hall, A. Bonifacio St.
   - Rivera Covered Court CLC: Rivera Covered Court Annex, F. Blumentritt St.
   - Pasadena CLC: Pasadena Daycare & ALS Center, F. Manalo St.

8. DIVISION OFFICE & CONTACTS:
   - Address: Division of City Schools - San Juan City, Pinaglabanan St., San Juan City 1500
   - Phone: +63 2 451 2699 | Fax: +63 2 451 2383
   - Email: depedsanjuanrecords@gmail.com
   - Program Officials:
     * Mr. Anselmo B. Joven (Education Program Supervisor – ALS) - 0917-053-0466
     * Mr. Erwin C. dela Cruz (Education Program Specialist II – ALS) - 0922-219-3356
     * Mobile Teachers: Mrs. Michelle Faye V. Salamera (0915-624-6983), Mrs. Hergelyn G. Cerredo (0933-820-0019), Mr. Emil O. Melchor (0975-983-9507)
     * Instructional Managers: Mr. Arniel Lago (0938-251-1138), Mr. Charles Andrew Cacajon (0908-878-5235)

TONE & BEHAVIOR:
- Warm, polite, inspiring, and supportive. Emphasize that it is never too late to finish education ("Sa ALS, may pag-asa ang pangarap!").
- Strictly follow the Language Rule: Tagalog for Tagalog queries, English for English queries.
- Format responses cleanly with brief bullet points or bold text for easy reading on mobile screens. Keep answers concise and direct.
PROMPT;
    }

    /**
     * Smart local knowledge fallback when API key is not configured or network fails.
     */
    private function getSmartFallbackResponse(string $question, bool $keyMissing): string
    {
        $q = mb_strtolower($question);

        $isEnglish = (bool) preg_match('/\b(who|what|where|when|why|how|requirement|center|program|enroll|free|fee|contact|is|are|can|hello|hi|good|morning|afternoon)\b/i', $question)
            && !preg_match('/\b(po|opo|ano|saan|paano|kailan|bakit|sino|meron|wala|ba|mga|ang|ng|sa|kay|si)\b/i', $question);

        $apiNote = $keyMissing 
            ? ($isEnglish 
                ? "\n\n💡 _Admin note: Please set `GEMINI_API_KEY` in your `.env` file for full generative reasoning._" 
                : "\n\n💡 _Tip para sa Admin: Mag-set ng `GEMINI_API_KEY` sa inyong `.env` file para sa full AI reasoning._")
            : "";

        // Refusal Guardrail: Coding, assignments, homework solving, non-school topics
        if (preg_match('/\b(code|coding|programming|python|javascript|php|html|css|java|c\+\+|sql|script|debug|assignment|homework|essay|test answer|exam answer|hack|gossip|dating)\b/i', $q)) {
            if ($isEnglish) {
                return "I apologize, but I am **ALSJay**, the official AI assistant for DepEd SDO San Juan City Alternative Learning System (ALS). I am designed to assist exclusively with school-related matters, such as ALS academic programs, enrollment requirements, learning centers, and official division announcements. I cannot assist with coding, homework solving, or non-school topics. How may I help you with your education or enrollment in ALS today?";
            }
            return "Paumanhin po, ako si **ALSJay**, ang opisyal na AI assistant para sa DepEd SDO San Juan City Alternative Learning System (ALS). Nakalaan lamang po ako para sa mga katanungan tungkol sa ating paaralan, mga programa sa ALS, requirements, enrollment, at opisyal na anunsyo. Hindi po ako sumasagot ng mga tanong ukol sa coding, paggawa ng assignments/homework, o mga paksang walang kinalaman sa ating paaralan. Paano po ako makakatulong sa inyong pag-aaral o pag-enroll sa ALS?";
        }

        // Introduction / Identity
        if (preg_match('/\b(who are you|who r u|sino ka|ano pangalan mo|what is your name|name)\b/i', $q)) {
            if ($isEnglish) {
                return "Hello! I am **ALSJay**, your official AI Virtual Assistant for DepEd Schools Division Office (SDO) of San Juan City — Alternative Learning System (ALS). I am here to assist you with information about our learning programs, enrollment requirements, and Community Learning Centers across San Juan City. How can I help you today?" . $apiNote;
            }
            return "Magandang araw po! Ako si **ALSJay**, ang opisyal na AI Virtual Assistant para sa DepEd Schools Division Office (SDO) ng San Juan City — Alternative Learning System (ALS). Narito po ako upang gabayan kayo tungkol sa mga programa (BLP at A&E), requirements sa pag-enroll, at mga Community Learning Center sa San Juan. Ano po ang maitutulong ko sa inyo ngayon?" . $apiNote;
        }

        if (str_contains($q, 'requirement') || str_contains($q, 'kailangan') || str_contains($q, 'dokument') || str_contains($q, 'document') || str_contains($q, 'psa') || str_contains($q, 'id photo')) {
            if ($isEnglish) {
                return "📋 **Enrollment Requirements for ALS San Juan City:**\n\n" .
                    "1. **PSA Birth Certificate** (or Local Civil Registrar Copy / Baptismal Certificate if unavailable)\n" .
                    "2. **Barangay Clearance / Certificate of Residency** (verifying residence in San Juan City)\n" .
                    "3. **2x2 ID Photos** (2 copies, white background, with printed name tag)\n" .
                    "4. **Form 137 / SF10** (Permanent school record from the last school attended, if available)\n\n" .
                    "You may register online via our [Online Registration](/register) page or visit your nearest Community Learning Center!" . $apiNote;
            }
            return "📋 **Mga Requirements sa Pag-enroll sa ALS San Juan:**\n\n" .
                "1. **PSA Birth Certificate** (o Local Civil Registrar Copy / Baptismal Certificate kung wala)\n" .
                "2. **Barangay Clearance / Certificate of Residency** (nagpapatunay ng tirahan sa San Juan City)\n" .
                "3. **2x2 ID Photos** (2 kopya, puti ang background, may name tag)\n" .
                "4. **Form 137 / SF10** (Permanent record mula sa huling paaralang pinasukan, kung mayroon)\n\n" .
                "Maaari kayong mag-register online sa ating [Online Registration](/register) o magtungo sa pinakamalapit na Community Learning Center!" . $apiNote;
        }

        if (str_contains($q, 'center') || str_contains($q, 'clc') || str_contains($q, 'saan') || str_contains($q, 'location') || str_contains($q, 'lugar') || str_contains($q, 'address')) {
            if ($isEnglish) {
                return "🏫 **Community Learning Centers (CLCs) in San Juan City:**\n\n" .
                    "We have 12 active learning centers in San Juan City, including:\n" .
                    "• **West Crame CLC** (3rd Floor, Brgy Hall)\n" .
                    "• **Addition Hills Integrated School CLC** (Room 102, ALS Bldg)\n" .
                    "• **San Juan National High School CLC** (DepEd Bldg, N. Domingo)\n" .
                    "• **Batis Barangay Hall Learning Center** (F. Manalo St.)\n" .
                    "• **Salapan Elementary School CLC** (F. Roman St.)\n" .
                    "• **Pedro Cruz CLC** (Covered Court Multi-Purpose)\n" .
                    "• **Kabayanan Elementary School CLC** (I. Lopez St.)\n" .
                    "• **Sta. Lucia, Balong-Bato, Rivera, Pasadena, and BJMP CLC**.\n\n" .
                    "Visit our [Learning Centers page](/learning-centers) for the complete directory." . $apiNote;
            }
            return "🏫 **Community Learning Centers (CLCs) sa San Juan City:**\n\n" .
                "Mayroon tayong 12 aktibong learning centers sa lungsod kabilang ang:\n" .
                "• **West Crame CLC** (3rd Floor, Brgy Hall)\n" .
                "• **Addition Hills Integrated School CLC** (Room 102, ALS Bldg)\n" .
                "• **San Juan National High School CLC** (DepEd Bldg, N. Domingo)\n" .
                "• **Batis Barangay Hall Learning Center** (F. Manalo St.)\n" .
                "• **Salapan Elementary School CLC** (F. Roman St.)\n" .
                "• **Pedro Cruz CLC** (Covered Court Multi-Purpose)\n" .
                "• **Kabayanan Elementary School CLC** (I. Lopez St.)\n" .
                "• **Sta. Lucia, Balong-Bato, Rivera, Pasadena, at BJMP CLC**.\n\n" .
                "Bisitahin ang ating [Learning Centers page](/learning-centers) para sa buong mapa at listahan." . $apiNote;
        }

        if (str_contains($q, 'program') || str_contains($q, 'a&e') || str_contains($q, 'blp') || str_contains($q, 'kurso') || str_contains($q, 'strand') || str_contains($q, 'elementary') || str_contains($q, 'high school')) {
            if ($isEnglish) {
                return "📚 **Programs Offered by ALS SDO San Juan:**\n\n" .
                    "1. **Basic Literacy Program (BLP)**: For learners seeking basic reading, writing, and numeracy skills.\n" .
                    "2. **A&E Elementary Level**: Grade 6 equivalency for learners aged 12 and above. Leads to an official elementary diploma.\n" .
                    "3. **A&E Junior High School Level**: Grade 10 equivalency for learners aged 16 and above. Qualifies completers for Senior High School or college.\n\n" .
                    "All courses cover the **6 ALS Learning Strands** (Communication, Science, Math, Life & Career, Self & Society, Digital Citizenship)!" . $apiNote;
            }
            return "📚 **Mga Programa ng ALS SDO San Juan:**\n\n" .
                "1. **Basic Literacy Program (BLP)**: Para sa mga nais matutong magbasa, magsulat, at magbilang (walang prerequisite).\n" .
                "2. **A&E Elementary Level**: Katumbas ng Grade 1-6 para sa may edad 12 pataas. May opisyal na Elementary Diploma.\n" .
                "3. **A&E Junior High School Level**: Katumbas ng Grade 7-10 para sa may edad 16 pataas. Kwalipikado sa Senior High School o kolehiyo.\n\n" .
                "Lahat ay sumusunod sa **6 ALS Learning Strands** (Communication, Science, Math, Life & Career, Self & Society, at Digital Citizenship)!" . $apiNote;
        }

        if (str_contains($q, 'bayad') || str_contains($q, 'fee') || str_contains($q, 'libre') || str_contains($q, 'free') || str_contains($q, 'magkano') || str_contains($q, 'tuition') || str_contains($q, 'cost')) {
            if ($isEnglish) {
                return "🎉 **ALS in San Juan City is 100% FREE!**\n\n" .
                    "There are zero tuition or registration fees. All self-learning modules and official assessments are provided free by DepEd as a public service." . $apiNote;
            }
            return "🎉 **100% LIBRE (FREE) ang ALS sa San Juan City!**\n\n" .
                "Walang anumang tuition fee o registration fee. Libre ang mga self-learning modules at libre rin ang pagsusulit. Ito ay opisyal na serbisyo publiko ng DepEd." . $apiNote;
        }

        if (str_contains($q, 'contact') || str_contains($q, 'tawag') || str_contains($q, 'number') || str_contains($q, 'email') || str_contains($q, 'telepono') || str_contains($q, 'division')) {
            if ($isEnglish) {
                return "📞 **Contact Information — SDO San Juan City ALS:**\n\n" .
                    "• **Address**: Division of City Schools, Pinaglabanan St., San Juan City 1500\n" .
                    "• **Telephone**: +63 2 451 2699\n" .
                    "• **Email**: depedsanjuanrecords@gmail.com\n" .
                    "• **Supervisor**: Mr. Anselmo B. Joven (0917-053-0466)\n" .
                    "• **Specialist**: Mr. Erwin C. dela Cruz (0922-219-3356)\n\n" .
                    "You may also visit our [Contact Page](/contact) for directions." . $apiNote;
            }
            return "📞 **Impormasyon sa Pakikipag-ugnayan (SDO San Juan City ALS):**\n\n" .
                "• **Address**: Division of City Schools, Pinaglabanan St., San Juan City 1500\n" .
                "• **Telephone**: +63 2 451 2699\n" .
                "• **Email**: depedsanjuanrecords@gmail.com\n" .
                "• **Supervisor**: Mr. Anselmo B. Joven (0917-053-0466)\n" .
                "• **Specialist**: Mr. Erwin C. dela Cruz (0922-219-3356)\n\n" .
                "Maaari rin kayong bumisita sa ating [Contact Page](/contact)." . $apiNote;
        }

        if (str_contains($q, 'paano') || str_contains($q, 'enroll') || str_contains($q, 'register') || str_contains($q, 'sumali') || str_contains($q, 'how to')) {
            if ($isEnglish) {
                return "📝 **How to Enroll in ALS San Juan City:**\n\n" .
                    "Two easy methods:\n" .
                    "1. **Online**: Visit our [Online Registration](/register) page and complete the digital ALS Form 2 (5-10 minutes).\n" .
                    "2. **In-Person**: Walk in to any Community Learning Center (CLC) or the Division Office at Pinaglabanan St.\n\n" .
                    "Prepare your PSA Birth Certificate, Barangay Clearance, and 2x2 ID photo!" . $apiNote;
            }
            return "📝 **Paano Mag-enroll sa ALS San Juan City:**\n\n" .
                "May dalawang madaling paraan:\n" .
                "1. **Online**: I-click lamang ang [Online Registration](/register) at punan ang ALS Form 2 (5-10 minuto lang).\n" .
                "2. **Walk-in**: Pumunta sa pinakamalapit na Community Learning Center (CLC) o sa San Juan Division Office sa Pinaglabanan St.\n\n" .
                "Ihanda lamang ang PSA Birth Certificate, Barangay Clearance, at 2x2 picture!" . $apiNote;
        }

        if (str_contains($q, 'weather') || str_contains($q, 'panahon') || str_contains($q, 'ulan') || str_contains($q, 'bagyo') || str_contains($q, 'suspension') || str_contains($q, 'pasok') || str_contains($q, 'suspensyon') || str_contains($q, 'forecast')) {
            if ($isEnglish) {
                return "🌧️ **Weather & Class Suspension Advisories (San Juan City):**\n\n" .
                    "For official real-time announcements regarding weather and class suspensions in San Juan City:\n" .
                    "• **San Juan City PIO**: Check the official Facebook page of the **San Juan City Public Information Office** or **Mayor Francis Zamora**.\n" .
                    "• **DOST-PAGASA**: View official weather bulletins at [bagong.pagasa.dost.gov.ph](https://bagong.pagasa.dost.gov.ph).\n\n" .
                    "Stay safe and monitor official local advisories!" . $apiNote;
            }
            return "🌧️ **Alerto sa Panahon at Suspensyon ng Klase (San Juan City):**\n\n" .
                "Para sa opisyal at pinakahuling anunsyo tungkol sa suspensyon ng klase at lagay ng panahon sa San Juan City:\n" .
                "• **San Juan City PIO**: Bisitahin ang opisyal na Facebook page ng **San Juan City Public Information Office** o ni **Mayor Francis Zamora**.\n" .
                "• **DOST-PAGASA**: Alamin ang pinakabagong lagay ng panahon sa [bagong.pagasa.dost.gov.ph](https://bagong.pagasa.dost.gov.ph).\n\n" .
                "Manatiling ligtas at subaybayan ang mga opisyal na babala!" . $apiNote;
        }

        if ($isEnglish) {
            return "Hello! I am **ALSJay**, your official AI Virtual Assistant for SDO San Juan City ALS.\n\n" .
                "Feel free to ask me about:\n" .
                "• 📋 **Enrollment Requirements**\n" .
                "• 🏫 **Community Learning Centers (CLCs)** in San Juan\n" .
                "• 📚 **Academic Programs** (BLP, A&E Elementary and High School)\n" .
                "• 📝 **Online Registration Steps**\n" .
                "• 📞 **Division Office & Teacher Contacts**\n\n" .
                "How may I help you today?" . $apiNote;
        }

        return "Magandang araw po! Ako si **ALSJay**, ang opisyal na AI Virtual Assistant para sa SDO San Juan City ALS.\n\n" .
            "Maaari po ninyo akong tanungin tungkol sa:\n" .
            "• 📋 **Mga Requirements** para sa pag-enroll\n" .
            "• 🏫 **Community Learning Centers (CLCs)** sa San Juan\n" .
            "• 📚 **Programa** (Basic Literacy at A&E Elementary/High School)\n" .
            "• 📝 **Paano mag-enroll online**\n" .
            "• 📞 **Contacts ng Division Office at Mobile Teachers**\n\n" .
            "Ano po ang nais ninyong malaman?" . $apiNote;
    }

    /**
     * Determine if a text is primarily Tagalog/Filipino or English.
     */
    private function isTagalog(string $text): bool
    {
        $hasTagalogWords = (bool) preg_match('/\b(po|opo|ano|saan|paano|kailan|bakit|sino|meron|merong|wala|walang|ba|mga|ang|ng|sa|kay|si|kumusta|kamusta|naman|ito|iyan|iyon|kayo|inyo|ninyo|ikaw|ka|pala|ko|mo|namin|natin|atin|lahat|ay|may|mayroon|magkano|pwede|pede|gusto|nais|bata|aral|paaralan|guro|titser|usapan|usap|tulong)\b/i', $text);

        return $hasTagalogWords;
    }

    /**
     * Check if a message violates school-only guardrails:
     * - Coding / programming requests
     * - Homework, assignment, or test solving
     * - Harassment / abuse / profanity
     */
    private function checkImmediateGuardrailRefusal(string $message): ?string
    {
        $q = mb_strtolower($message);
        $isTagalog = $this->isTagalog($message);

        // Coding requests (e.g. write code, python, javascript, php, css, debug code, programming, etc.)
        $isCoding = preg_match('/\b(write|create|generate|debug|fix|solve|give me|make)\b.*\b(code|python|javascript|script|html|css|php|java|c\+\+|c#|sql|function|algorithm|program)\b/i', $q)
            || preg_match('/\b(python|javascript|php|html|css|c\+\+|c#|sql)\b.*\b(code|script|function|program|loop|class|array)\b/i', $q)
            || preg_match('/\b(code|coding|programming|programmer|software development)\b/i', $q);

        // Homework / assignment solving (e.g. solve my homework, gawin mo assignment ko, etc.)
        $isHomework = preg_match('/\b(gawin mo|sagutan mo|sagot sa|sagot mo|solve this|solve my|do my|answer my|do this)\b.*\b(assignment|homework|exam|test|quiz|takdang-aralin|pagsusulit)\b/i', $q)
            || preg_match('/\b(assignment|homework|takdang-aralin)\b.*\b(gawin mo|sagutan mo|solve|answer)\b/i', $q)
            || (preg_match('/\b(assignment|homework|takdang-aralin)\b/i', $q) && preg_match('/\b(sagot|solve|gawin|do|answer|write)\b/i', $q));

        // Harassment / abuse / profanity
        $isHarassment = preg_match('/\b(tangina|tarantado|gago|putangina|ulol|bobo|tanga|pakyu|fuck|bitch|bastard|asshole|idiot|stupid|harass)\b/i', $q);

        if ($isCoding || $isHomework || $isHarassment) {
            if ($isTagalog) {
                return "Paumanhin po, ako si **ALSJay**, ang opisyal na AI assistant para sa DepEd SDO San Juan City Alternative Learning System (ALS).\n\n" .
                    "Nakalaan lamang po ako para sa mga katanungan tungkol sa ating paaralan, mga programa sa ALS (BLP at A&E), requirements, enrollment, mga Community Learning Center, at opisyal na anunsyo ng Dibisyon.\n\n" .
                    "Hindi po ako pinahihintulutang sumagot ng mga tanong ukol sa **coding o programming**, **paggawa o pagsagot ng mga takdang-aralin (assignments/homework)**, harassment, o mga paksang walang kinalaman sa ating paaralan.\n\n" .
                    "Paano po ako makakatulong sa inyong pag-aaral o pag-enroll sa ALS San Juan ngayon?";
            } else {
                return "I apologize, but I am **ALSJay**, the official AI Virtual Assistant for DepEd SDO San Juan City Alternative Learning System (ALS).\n\n" .
                    "I am dedicated exclusively to school-related matters—such as ALS educational programs (BLP, A&E Elementary and Junior High School), enrollment requirements, Community Learning Centers, and official division announcements.\n\n" .
                    "I cannot assist with **coding or programming**, **solving homework, assignments, or tests**, harassment, or non-school topics.\n\n" .
                    "How may I assist you with your education or enrollment in ALS San Juan today?";
            }
        }

        return null;
    }
}

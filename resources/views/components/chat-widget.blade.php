<!-- ALS SDO San Juan City - AI Virtual Assistant Floating Widget -->
<style>
    /* Slow-motion gentle jumping (up and down) animation */
    @keyframes alsSlowJump {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-12px);
        }
    }
    .animate-slow-jump {
        animation: alsSlowJump 3s ease-in-out infinite;
    }
</style>

<div x-data="alsChatbot()" 
     x-cloak
     class="fixed bottom-5 right-5 sm:bottom-6 sm:right-6 z-50 font-sans flex flex-col items-end pointer-events-none"
     @keydown.escape.window="isOpen = false">

    <!-- 1. CHAT DIALOG WINDOW (Pops Up Directly Above the Bubble) -->
    <div x-show="isOpen" 
         x-transition:enter="transition cubic-bezier(0.16, 1, 0.3, 1) duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4"
         class="pointer-events-auto mb-3.5 w-[calc(100vw-2rem)] sm:w-[410px] max-w-[420px] h-[580px] max-h-[calc(100vh-115px)] bg-white rounded-3xl shadow-2xl border border-slate-200/90 flex flex-col overflow-hidden backdrop-blur-xl origin-bottom-right">

        <!-- HEADER -->
        <div class="px-5 py-4 bg-gradient-to-r from-slate-950 via-deped-navy to-deped-blue text-white flex items-center justify-between border-b border-white/10 shrink-0">
            <div class="flex items-center gap-3">
                <div class="relative w-10 h-10 rounded-2xl bg-white p-1 flex items-center justify-center border border-white/20 shadow-inner overflow-hidden">
                    <img src="{{ file_exists(public_path('images/sdosj.svg')) ? asset('images/sdosj.svg') : (file_exists(public_path('images/school_logo.png')) ? asset('images/school_logo.png') : asset('images/school_logo.svg')) }}" 
                         alt="SDO San Juan" 
                         class="w-full h-full object-contain">
                    <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-500 border-2 border-slate-900 rounded-full"></span>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-black tracking-tight text-white">ALSJay</h3>
                        <span class="text-[9px] font-black uppercase px-1.5 py-0.2 rounded bg-deped-gold text-slate-950 tracking-wider">AI</span>
                    </div>
                    <p class="text-[11px] text-slate-300 font-medium flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        DepEd SDO San Juan &bull; Online
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-1">
                <!-- Clear Chat -->
                <button @click="resetConversation()" 
                        type="button" 
                        title="Reset conversation"
                        class="p-2 rounded-xl text-slate-300 hover:text-white hover:bg-white/10 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </button>
                <!-- Close Window -->
                <button @click="isOpen = false" 
                        type="button" 
                        title="Close chat"
                        class="p-2 rounded-xl text-slate-300 hover:text-white hover:bg-white/10 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- MESSAGES STREAM CONTAINER -->
        <div id="alsChatMessages" 
             class="flex-1 p-4 overflow-y-auto space-y-4 bg-slate-50/70 custom-scrollbar text-xs sm:text-sm">
            
            <template x-for="(msg, index) in messages" :key="index">
                <div>
                    <!-- ASSISTANT MESSAGE -->
                    <template x-if="msg.role === 'model' || msg.role === 'assistant'">
                        <div class="flex items-start gap-2.5 max-w-[92%]">
                            <div class="w-7 h-7 rounded-xl bg-white text-deped-gold flex items-center justify-center shrink-0 shadow-sm p-1 border border-slate-200">
                                <img src="{{ file_exists(public_path('images/sdosj.svg')) ? asset('images/sdosj.svg') : (file_exists(public_path('images/school_logo.png')) ? asset('images/school_logo.png') : asset('images/school_logo.svg')) }}" 
                                     alt="AI" 
                                     class="w-full h-full object-contain">
                            </div>
                            <div class="space-y-1">
                                <div class="bg-white p-3.5 rounded-2xl rounded-tl-sm border border-slate-200/90 shadow-sm text-slate-800 leading-relaxed font-normal"
                                     x-html="formatMarkdown(msg.text)">
                                </div>
                                <span class="text-[10px] text-slate-400 font-medium pl-1" x-text="msg.time"></span>
                            </div>
                        </div>
                    </template>

                    <!-- USER MESSAGE -->
                    <template x-if="msg.role === 'user'">
                        <div class="flex items-end justify-end gap-2 ml-auto max-w-[85%]">
                            <div class="space-y-1 text-right">
                                <div class="bg-deped-blue text-white p-3.5 rounded-2xl rounded-tr-sm shadow-md leading-relaxed font-normal text-left"
                                     x-text="msg.text">
                                </div>
                                <span class="text-[10px] text-slate-400 font-medium pr-1" x-text="msg.time"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- THINKING / TYPING INDICATOR -->
            <div x-show="isLoading" class="flex items-center gap-2 text-slate-500 py-1">
                <div class="w-7 h-7 rounded-xl bg-deped-navy/10 flex items-center justify-center p-1 text-deped-blue">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </div>
                <div class="bg-white border border-slate-200 px-3.5 py-2 rounded-2xl rounded-tl-sm shadow-sm flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-deped-blue animate-bounce"></span>
                    <span class="w-2 h-2 rounded-full bg-deped-blue animate-bounce [animation-delay:0.2s]"></span>
                    <span class="w-2 h-2 rounded-full bg-deped-blue animate-bounce [animation-delay:0.4s]"></span>
                    <span class="text-[11px] font-semibold text-slate-600 pl-1">ALSJay is responding...</span>
                </div>
            </div>

            <!-- QUICK SUGGESTIONS CHIPS (Shown only when 1 message is in chat) -->
            <div x-show="messages.length <= 1" class="pt-2 space-y-2">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Mga Karaniwang Tanong:</p>
                <div class="flex flex-wrap gap-1.5">
                    <template x-for="(prompt, pIdx) in quickPrompts" :key="pIdx">
                        <button @click="sendQuickPrompt(prompt)" 
                                type="button"
                                class="px-3 py-1.5 rounded-xl bg-white hover:bg-blue-50 text-slate-700 hover:text-deped-blue border border-slate-200/90 text-[11px] font-semibold text-left transition-colors shadow-2xs">
                            <span x-text="prompt"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <!-- INPUT BOX FOOTER -->
        <div class="p-3 bg-white border-t border-slate-200 shrink-0">
            <form @submit.prevent="submitMessage()" class="flex items-center gap-2">
                <input type="text" 
                       x-model="inputText" 
                       x-ref="messageInput"
                       :disabled="isLoading"
                       placeholder="Magtanong tungkol sa ALS San Juan..." 
                       class="flex-1 px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs sm:text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-deped-blue/30 focus:border-deped-blue transition-all disabled:opacity-50">
                
                <button type="submit" 
                        :disabled="isLoading || !inputText.trim()"
                        aria-label="Send message"
                        class="w-10 h-10 rounded-2xl bg-deped-blue hover:bg-deped-dark text-white flex items-center justify-center transition-all disabled:opacity-40 disabled:cursor-not-allowed shadow-md hover:shadow-lg active:scale-95 shrink-0">
                    <svg class="w-4 h-4 rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19V5m0 0l-7 7m7-7l7 7"/>
                    </svg>
                </button>
            </form>
            <div class="mt-2 text-center text-[10px] text-slate-400">
                SDO San Juan City &bull; Libreng Edukasyon para sa Lahat
            </div>
        </div>

    </div>

    <!-- 2. CALLOUT / SPEECH BUBBLE (Visible only when closed) -->
    <div x-show="!isOpen && showCallout" 
         x-transition:enter="transition ease-out duration-300 delay-150"
         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-90"
         class="pointer-events-auto mb-3 max-w-[270px] sm:max-w-[310px] select-none">
        
        <div @click="toggleChat()" 
             class="cursor-pointer group relative bg-white/98 rounded-2xl py-2.5 px-3.5 border border-slate-200/90 hover:border-deped-blue/60 shadow-lg hover:shadow-xl transition-all duration-300 flex items-center gap-2.5">
            
            <!-- Friendly Avatar Wave -->
            <div class="w-8 h-8 rounded-xl bg-deped-blue/10 text-deped-blue flex items-center justify-center shrink-0 border border-deped-blue/20">
                <span class="text-base">👋</span>
            </div>

            <!-- Callout Message -->
            <div class="flex-1 min-w-0 pr-1">
                <div class="flex items-center gap-1.5 mb-0.5">
                    <span class="text-[10px] font-black uppercase tracking-wider text-deped-blue">ALSJay</span>
                </div>
                <p class="text-xs sm:text-[13px] font-bold text-slate-800 leading-tight group-hover:text-deped-blue transition-colors">
                    Ano ang maitutulong ko sa’yo ngayon?
                </p>
            </div>

            <!-- Dismiss Button -->
            <button @click.stop="showCallout = false" 
                    type="button" 
                    title="Isara ang paalala"
                    class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <!-- Speech Bubble Tail pointing down toward circular FAB -->
            <div class="absolute -bottom-2 right-6 sm:right-7 w-3.5 h-3.5 bg-white border-r border-b border-slate-200/90 rotate-45 transform"></div>
        </div>
    </div>

    <!-- 3. CIRCULAR CHAT BUBBLE BUTTON (Stays in its exact bottom-right position) -->
    <div class="pointer-events-auto shrink-0" :class="{'animate-slow-jump': !isOpen}">
        <button @click="toggleChat()" 
                type="button"
                :title="isOpen ? 'Isara ang ALSJay Chat' : 'Buksan ang ALSJay Chat'"
                aria-label="Toggle ALSJay AI Assistant"
                class="group relative w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-gradient-to-tr from-deped-navy via-slate-900 to-deped-blue text-white shadow-xl hover:shadow-2xl border-2 border-white/30 hover:scale-105 active:scale-95 transition-all duration-300 flex items-center justify-center">
            
            <!-- STATE A: Closed (Show SDO San Juan logo) -->
            <div x-show="!isOpen" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-75 rotate-45"
                 x-transition:enter-end="opacity-100 scale-100 rotate-0"
                 class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white p-1 flex items-center justify-center overflow-hidden shadow-sm">
                <img src="{{ file_exists(public_path('images/sdosj.svg')) ? asset('images/sdosj.svg') : (file_exists(public_path('images/school_logo.png')) ? asset('images/school_logo.png') : asset('images/school_logo.svg')) }}" 
                     alt="SDO San Juan Emblem" 
                     class="w-full h-full object-contain">
            </div>

            <!-- STATE B: Open (Show Close X icon) -->
            <div x-show="isOpen" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-75 -rotate-45"
                 x-transition:enter-end="opacity-100 scale-100 rotate-0"
                 class="w-6 h-6 flex items-center justify-center text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>

            <!-- Online Status Dot (Top-Right, only when closed) -->
            <span x-show="!isOpen" class="absolute top-0 right-0 flex h-3.5 w-3.5">
                <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-500 border-2 border-slate-950"></span>
            </span>
        </button>
    </div>

</div>

<script>
function alsChatbot() {
    return {
        isOpen: false,
        showCallout: true,
        isLoading: false,
        inputText: '',
        messages: [
            {
                role: 'model',
                text: 'Mabuhay! 👋 Ako si **ALSJay**, ang inyong opisyal na **ALS SDO San Juan AI Virtual Assistant**.\n\nHanda akong tumulong sa inyong mga katanungan tungkol sa **Requirements**, **Community Learning Centers (CLCs)**, **Programs (A&E, BLP)**, **Online Registration**, o **Weather & Suspensions**.\n\nAno po ang maitutulong ko sa inyo ngayon?',
                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
            }
        ],
        quickPrompts: [
            '📋 Ano ang requirements sa pag-enroll?',
            '🏫 Saan ang mga Learning Centers sa San Juan?',
            '📚 Ano ang pagkakaiba ng BLP at A&E?',
            '🌤️ Ano ang weather forecast ngayon?',
            '💸 May bayad ba ang pag-aaral sa ALS?',
            '📝 Paano mag-register online?'
        ],

        toggleChat() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.showCallout = false;
                this.$nextTick(() => {
                    this.scrollToBottom();
                    if (this.$refs.messageInput) {
                        this.$refs.messageInput.focus();
                    }
                });
            }
        },

        resetConversation() {
            this.messages = [
                {
                    role: 'model',
                    text: 'Mabuhay muli! Nareset na po ang ating usapan. Ako muli si **ALSJay**, may maitutulong po ba ako sa inyo tungkol sa DepEd ALS San Juan?',
                    time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                }
            ];
            this.scrollToBottom();
        },

        sendQuickPrompt(promptText) {
            this.inputText = promptText;
            this.submitMessage();
        },

        async submitMessage() {
            const text = this.inputText.trim();
            if (!text || this.isLoading) return;

            const timeNow = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            // Push User Message
            this.messages.push({
                role: 'user',
                text: text,
                time: timeNow
            });

            this.inputText = '';
            this.isLoading = true;
            this.scrollToBottom();

            // Prepare history (last 6 turns)
            const history = this.messages
                .slice(-7, -1)
                .map(m => ({
                    role: m.role === 'user' ? 'user' : 'model',
                    text: m.text
                }));

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            try {
                const response = await fetch('/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        message: text,
                        history: history
                    })
                });

                if (!response.ok) {
                    throw new Error(`Server returned ${response.status}`);
                }

                const data = await response.json();
                const replyText = data.reply || 'Pasensya na po, nagkaroon ng saglit na aberya. Pakisubukan muli.';

                this.messages.push({
                    role: 'model',
                    text: replyText,
                    time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                });

            } catch (error) {
                console.error('Chat error:', error);
                this.messages.push({
                    role: 'model',
                    text: 'Pasensya na po, hindi maabot ang server sa ngayon. Pakitiyak na maayos ang inyong koneksyon sa internet o subukan muli maya-maya.',
                    time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                });
            } finally {
                this.isLoading = false;
                this.scrollToBottom();
            }
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = document.getElementById('alsChatMessages');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        },

        formatMarkdown(rawText) {
            if (!rawText) return '';
            let escaped = rawText
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');

            // Bold **text**
            escaped = escaped.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            // Italic *text* or _text_
            escaped = escaped.replace(/\*(.*?)\*/g, '<em>$1</em>');
            escaped = escaped.replace(/_(.*?)_/g, '<em>$1</em>');

            // Markdown Links [Text](URL)
            escaped = escaped.replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" class="text-deped-blue font-bold underline hover:text-deped-dark" target="_blank" rel="noopener noreferrer">$1</a>');

            // Bullet points
            escaped = escaped.replace(/(?:^|\n)[•\-]\s+(.+)/g, '<div class="flex items-start gap-1.5 my-1"><span class="text-deped-blue font-black">•</span><span>$1</span></div>');

            // Line breaks
            escaped = escaped.replace(/\n/g, '<br>');

            return escaped;
        }
    };
}
</script>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>K-12 Smart Tutor - Sir Dhine</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        #chat::-webkit-scrollbar {
            width: 8px;
        }

        #chat::-webkit-scrollbar-thumb {
            background: #93c5fd;
            border-radius: 9999px;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-indigo-100 via-sky-50 to-blue-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-5xl p-6">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-lg p-5 mb-6">
            <div class="flex items-center gap-4">
                <div
                    class="w-14 h-14 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-xl">
                    SD
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-indigo-800">K-12 Smart Tutor</h1>
                    <p class="text-sm text-gray-600">Your all-in-one learning assistant</p>
                </div>
            </div>
        </div>

        <!-- Chat Box -->
        <div class="bg-white rounded-2xl shadow-xl p-5 mb-4 border border-blue-200">
            <div id="chat" class="h-96 overflow-y-auto space-y-3 pr-2">
                <div class="bg-blue-50 border border-blue-200 p-3 rounded-xl text-gray-700 text-sm">
                    👋 Hi! I'm your K-12 Smart Tutor. Type your question below and I'll help you!
                </div>
            </div>
        </div>

        <!-- Input -->
        <div class="flex items-center gap-3">
            <input id="msg" type="text"
                class="flex-1 p-4 border-2 border-blue-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 text-base"
                placeholder="Type your question here..." />
            <button id="send-btn"
                class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 transition-all text-white px-6 py-4 rounded-xl font-semibold shadow-lg">
                Send
            </button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="assets/js/chatbot.js"></script>
</body>

</html>
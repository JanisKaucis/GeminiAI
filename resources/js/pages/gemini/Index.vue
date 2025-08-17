<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';
import { LogOut } from 'lucide-vue-next';
import { defineProps, onMounted, ref } from 'vue';

const props = defineProps({
    conversations: Object,
});
const question = ref('');
const errors = ref({});
const answer = ref('');
const loading = ref(false);
const conversationsHistory = ref(props.conversations);
const selectedWindowId = ref<string | null>(null);
const firstQuestion = ref(false);

function askGemini() {
    loading.value = true;
    errors.value = {};

    if (!sessionStorage.getItem('window_id')) {
        sessionStorage.setItem('window_id', crypto.randomUUID());
        firstQuestion.value = true;
    }
    const windowId = sessionStorage.getItem('window_id');

    axios
        .post(route('ask-gemini', { question: question.value, window_id: windowId }))
        .then((response) => {
            const data = response.data;

            answer.value += '<div class="inline-block p-2 m-2 ml-0 bg-gray-400 rounded-md">' + question.value + '</div>\n';
            answer.value += data.answer + '\n';
            question.value = '';
            loading.value = false;
        })
        .catch((error) => {
            if (error.response?.status !== 200) {
                errors.value = error.response.data.errors;
                loading.value = false;
                question.value = '';
            }
        });

    if (firstQuestion.value) {
        updateConversationsHistory(windowId);
    }
}

function getThisSessionChatMessages() {
    if (!sessionStorage.getItem('window_id')) {
        return;
    }
    const windowId = sessionStorage.getItem('window_id');

    selectConversation(windowId);
}

function selectConversation(windowId: string | null) {
    answer.value = '';
    selectedWindowId.value = windowId;
    sessionStorage.setItem('window_id', windowId ?? crypto.randomUUID());

    axios.get(route('chat-window-history', { window_id: windowId })).then((response) => {
        const messages = response.data.messages;

        for (let i = 0; i < messages.length; i++) {
            if (messages[i].role === 'user') {
                answer.value += '<div class="inline-block p-2 m-2 ml-0 bg-gray-400 rounded-md">' + messages[i].message + '</div>\n';
            }
            if (messages[i].role === 'model') {
                answer.value += messages[i].message + '\n';
            }
        }
    });
}

function updateConversationsHistory(windowId: string | null) {
    axios.get(route('conversations-history')).then((response) => {
        conversationsHistory.value = response.data.conversations;
        selectedWindowId.value = windowId;
    });
}

function startNewConversation() {
    sessionStorage.removeItem('window_id');
    selectedWindowId.value = null;
    answer.value = '';
}

onMounted(() => {
    getThisSessionChatMessages();
});
</script>

<template>
    <div class="grid grid-cols-4 bg-black">
        <div class="col-span-4 flex h-full rounded border bg-gray-200 p-4 text-black lg:col-span-1 lg:mx-4">
            <div class="w-full">
                <button class="rounded border border-gray-500 bg-gray-300 p-1 hover:cursor-pointer" @click="startNewConversation()">
                    + New chat
                </button>
                <div class="mb-2 border-b-2 pb-2">Chats</div>
                <div v-for="conversation in conversationsHistory" :key="conversation.id">
                    <div class="my-1">
                        <button
                            :title="conversation.title"
                            class="w-80 lg:w-40 truncate text-left hover:cursor-pointer hover:rounded hover:bg-gray-300"
                            :class="{
                                'rounded border border-gray-500 bg-gray-300': selectedWindowId === conversation.window_id,
                            }"
                            type="button"
                            @click="selectConversation(conversation.window_id)"
                        >
                            {{ conversation.title }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-4 flex h-screen w-full flex-col justify-self-center rounded bg-gray-400 p-4 lg:col-span-2">
            <div class="mb-2 flex-1 overflow-x-hidden rounded bg-gray-100 shadow">
                <div v-if="errors?.failed">
                    <div class="m-2 inline-block rounded-md bg-red-300 p-2 text-red-500">{{ errors.failed }}</div>
                </div>
                <div v-if="answer">
                    <pre class="h-full overflow-x-hidden overflow-y-auto p-4 break-words whitespace-pre-wrap text-black" v-html="answer"></pre>
                </div>
            </div>
            <div class="mt-auto w-full rounded-lg bg-white p-6">
                <form @submit.prevent="askGemini">
                    <div class="flex flex-col">
                        <input class="rounded-lg bg-gray-200 p-2 text-black" v-model="question" />
                        <p v-if="errors?.question" class="text-sm text-red-600">{{ errors.question[0] }}</p>
                        <div class="flex justify-center">
                            <Button class="m-4 flex h-10 w-30 content-center bg-blue-300 hover:bg-blue-400" type="submit" :disabled="loading">
                                <span v-if="loading" class="h-5 w-5 animate-spin rounded-full border-4 border-white border-t-transparent"></span
                                ><span v-if="!loading">Ask Gemini</span>
                            </Button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-span-4 lg:col-span-1 flex flex-col items-center">
            <Link class="flex hover:cursor-pointer" method="post" :href="route('logout')" as="button">
                    <LogOut class="mr-2 h-6 w-6" />
                    Log out
            </Link>
        </div>
    </div>
</template>

<style scoped></style>

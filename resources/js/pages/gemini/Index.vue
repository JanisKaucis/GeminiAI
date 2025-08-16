<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { ref } from 'vue';
import axios from 'axios';

const question = ref('');
const errors = ref({});
const answer = ref('');
const loading = ref(false);

function askGemini() {
    loading.value = true;
    errors.value = {};

    axios.post(route('ask-gemini', { question: question.value }))
        .then((response) => {
            const data = response.data;

            if (response.status !== 200) {
                errors.value = data.errors;
                loading.value = false;
                console.log(errors);
            }
            answer.value = '<div class="inline-block p-2 m-2 ml-0 bg-gray-400 rounded-md">' + question.value + '</div>\n';
            answer.value += data.answer;
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
}
</script>

<template>
    <div class="bg-black">
        <div class="flex h-screen w-1/2 flex-col justify-self-center bg-gray-400 p-4">
            <div class="mb-2 flex-1 overflow-x-hidden rounded bg-gray-100 shadow">
<!--                <div v-if="errors.failed">-->
<!--                    <div class="m-2 inline-block rounded-md bg-red-300 p-2 text-red-500">{{ errors.failed }}</div>-->
<!--                </div>-->
                <div v-if="answer">
                    <pre class="h-full overflow-x-hidden overflow-y-auto p-4 break-words whitespace-pre-wrap text-black" v-html="answer"></pre>
                </div>
            </div>
            <div class="mt-auto w-full rounded-lg bg-white p-6">
                <form @submit.prevent="askGemini">
                    <div class="flex flex-col">
                        <input class="rounded-lg bg-gray-200 p-2 text-black" v-model="question" />
<!--                        <p v-if="errors.question" class="text-sm text-red-600">{{ errors.question[0] }}</p>-->
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
    </div>
</template>

<style scoped></style>

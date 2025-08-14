<script setup lang="ts">
import { Button } from '@/components/ui/button';
import axios from 'axios';
import { ref } from 'vue';

const question = ref('');
const errors = ref({});
const answer = ref('');

function submitForm() {
    errors.value = {};
    axios
        .post(route('ask-gemini', { question: question.value }))
        .then((response) => {
            const data = response.data;

            if (response.status !== 200) {
                errors.value = data.errors;
                return;
            }

            answer.value = data.answer;
            question.value = '';
        })
        .catch((error) => {
            if (error.response?.status === 422) {
                errors.value = error.response.data.errors;
            }
        });
}
</script>

<template>
    <div class="bg-black">
        <div class="flex h-screen w-1/2 flex-col justify-self-center bg-gray-400 p-4">
            <div class="flex-1 overflow-hidden rounded bg-gray-100 shadow mb-2" v-if="answer">
                <pre class="h-full overflow-x-hidden overflow-y-auto p-4 break-words whitespace-pre-wrap text-black">
                    {{ answer }}</pre>
            </div>
            <div class="mt-auto w-full rounded-lg bg-white p-6">
                <form @submit.prevent="submitForm">
                    <div class="flex flex-col">
                        <input class="rounded-lg bg-gray-200 p-2 text-black" v-model="question" />
                        <p v-if="errors.question" class="text-sm text-red-600">{{ errors.question[0] }}</p>
                        <div class="flex justify-center">
                            <Button class="m-4 bg-blue-300" type="submit">Ask Gemini</Button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped></style>

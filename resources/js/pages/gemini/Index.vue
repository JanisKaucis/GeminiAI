<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
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
    <div>
        <form @submit.prevent="submitForm">
            <Input v-model="question" />
            <p v-if="errors.question" class="text-sm text-red-600">{{ errors.question[0] }}</p>
            <Button type="submit">Ask Gemini</Button>
            <div v-if="answer">
                <p>{{ answer }}</p>
            </div>
        </form>
    </div>
</template>

<style scoped></style>

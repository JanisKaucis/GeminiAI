Setup instructions:
# 🚀 GeminiAI – Laravel + Vue + Docker

This project is a **Laravel 12 + Vue 3** application running with MySQL.

---

## 🛠 Requirements
- Git

---

## ⚡ Setup Instructions

### 1. Clone the Repository

git clone https://github.com/JanisKaucis/GeminiAI.git

Open project.

composer install

copy .env.example .env

php artisan key:generate

Setup database in .env

php artisan migrate

npm install

npm run dev

php artisan serve


## What is this application:

The Gemini Chat Module allows authenticated users to interact with an AI assistant (Gemini). Users can:

Ask questions.

Have Multi-turn Conversations.

View responses in real-time.

Maintain multiple chat windows/conversations.

Fetch conversation history.

The module is implemented using Laravel 12, Inertia.js, Vue 3, and Tailwind CSS.

## API endpoint documentation:

All endpoints are protected by **auth + email verification** middleware.  
Authentication is required.

POST /ask-gemini  

Send a question to Gemini and receive an AI-generated answer.  

Rate limiting: /ask-gemini is limited to 20 requests per minute per user.

GET /chat-window-history

Fetch all messages for a given conversation window.

GET /conversations-history

Fetch last 10 conversations for the logged-in user.


## Example requests and responses:

Example Request (Axios)

axios.post('/ask-gemini', {
  question: "What is Docker?",
  window_id: "a1b2c3d4-uuid"
}).then(res => console.log(res.data));

Example Response
{
  "answer": "Docker is a platform that allows you to package and run applications in isolated containers."
}

Example Request (Axios)

axios.get('/chat-window-history', {
  params: { window_id: "a1b2c3d4-uuid" }
}).then(res => console.log(res.data));

Example Response

{
  "messages": [
    {
      "role": "user",
      "message": "Hello Gemini"
    },
    {
      "role": "model",
      "message": "Hi there! How can I help you today?"
    }
  ]
}

Example Request (Axios)

axios.get('/conversations-history')
  .then(res => console.log(res.data));

Example Response

{
  "conversations": [
    {
      "id": 1,
      "title": "What is Docker?",
      "window_id": "a1b2c3d4-uuid",
      "created_at": "2025-08-17T10:20:30Z"
    },
    {
      "id": 2,
      "title": "Explain Laravel Sail",
      "window_id": "d4c3b2a1-uuid",
      "created_at": "2025-08-17T11:00:00Z"
    }
  ]
}

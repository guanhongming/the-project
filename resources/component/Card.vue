<template>
    <div  class="message-card">
      <div v-if="onError===true" class="alert">
        {{ errorMessage }}
      </div>
      <div v-else class="hint">
        <p>{{ hintText }}</p>
        <router-link :to="hintLink">{{ hintLinkText }}</router-link>
      </div>
    </div>
  </template>

  <script>
  export default {
    name: 'MessageCard',
    props: {
        onError:{
        type:Boolean,
        required:true,
    },
      errorMessage: {
        type: String,

      },
      hintText: {
        type: String,

      },
      hintLink: {
        type: String,

      },
      hintLinkText: {
        type: String,

      }
    }
  }
  </script>

  <style scoped>
  @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');


*, *::before, *::after {
  font-family: 'Roboto', sans-serif;
}
.message-card a {
    .message-card {
  padding: 15px;
  background-color: #f0eeee;
  border-radius: 4px;
  text-align: center;
  margin-top: 10px;
  color: #333333;
  font-size: 10px;
}

  color: #000000;
  text-decoration: none;
  transition: opacity 0.3s ease;
}

.message-card a:hover {
  opacity: 0.4;
}

  </style>

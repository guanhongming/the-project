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

/* Apply the font to specific elements */
*, *::before, *::after {
  font-family: 'Roboto', sans-serif;
}
.message-card a {
    .message-card {
  padding: 15px;
  background-color: #f0eeee; /* Updated background color */
  border-radius: 4px;
  text-align: center;
  margin-top: 10px;
  color: #333333; /* Updated text color */
  font-size: 10px; /* Adjusted font size */
}

  color: #000000; /* Link text color */
  text-decoration: none;
  transition: opacity 0.3s ease;
}

.message-card a:hover {
  opacity: 0.4; /* Reduced opacity on hover */
}

  </style>

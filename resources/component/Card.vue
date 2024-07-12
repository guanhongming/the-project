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
  .message-card {
    border: 1px solid #ddd;
    padding: 1em;
    margin: 1em 0;
    border-radius: 5px;
  }
  .alert {
    font-size: 0.9em;
    color: red;
  }
  .hint {
    font-size: 0.9em;
  }
  </style>
  
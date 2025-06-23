<template>
    <div class="login-layout">
  
      <div class="login-container">
        <slot />
      </div>
    </div>
  </template>
  
  <style scoped>
  .login-layout {
    min-height: 100vh;
    width: 100%;
    display: flex;
    align-items: center;
  }
  .image{
    width: 50%;
    display: flex;
    justify-content: center;
  }
  
  .login-container {
    width: 100%;
    height: 100vh;
  
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }
  </style>
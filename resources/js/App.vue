<template>
  <div id="app">
    <router-view />
  </div>
</template>

<script>
export default {
  name: 'App'
}
</script>

<style>
/* Global styles */
@import 'bootstrap/dist/css/bootstrap.min.css';
@import '@fortawesome/fontawesome-free/css/all.min.css';

:root {
  --primary-brown: #996600;
  --secondary-brown: #b37400;
  --accent-brown: #cc9900;
  --dark-brown: #7a5200;
  --light-brown: #fff9e6;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  background-color: #ffffff;
  color: #1a1a2e;
}

a {
  text-decoration: none;
}

.btn {
  border-radius: 8px;
  font-weight: 500;
  transition: all 0.3s ease;
}

.btn-primary {
  background: linear-gradient(135deg, #b37400, #996600);
  border: none;
  color: white;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #996600, #7a5200);
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(153, 102, 0, 0.3);
}
</style>
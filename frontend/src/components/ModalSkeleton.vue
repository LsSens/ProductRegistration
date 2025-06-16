<template>
  <div class="modal-content skeleton">
    <div class="skeleton-header">
      <div class="skeleton-title"></div>
    </div>
    <div class="skeleton-body">
      <div class="skeleton-line"></div>
      <div class="skeleton-line"></div>
      <div class="skeleton-line"></div>
      <div class="skeleton-line"></div>
    </div>
    <div class="skeleton-footer">
      <div class="skeleton-button"></div>
      <div class="skeleton-button"></div>
    </div>
  </div>
</template>

<style scoped>
.skeleton {
  background: #fff;
  padding: 2rem;
  border-radius: 8px;
  width: 90%;
  max-width: 500px;
}

.skeleton-header {
  margin-bottom: 2rem;
}

.skeleton-title {
  height: 32px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: 4px;
  width: 60%;
  margin: 0 auto;
}

.skeleton-body {
  margin-bottom: 2rem;
}

.skeleton-line {
  height: 24px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: 4px;
  margin-bottom: 1rem;
}

.skeleton-line:nth-child(1) { width: 100%; }
.skeleton-line:nth-child(2) { width: 90%; }
.skeleton-line:nth-child(3) { width: 95%; }
.skeleton-line:nth-child(4) { width: 85%; }

.skeleton-footer {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}

.skeleton-button {
  height: 40px;
  width: 120px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: 4px;
}

@keyframes shimmer {
  0% {
    background-position: -200% 0;
  }
  100% {
    background-position: 200% 0;
  }
}

@media (max-width: 700px) {
  .skeleton {
    width: 95%;
    margin: 1rem;
  }
}
</style> 
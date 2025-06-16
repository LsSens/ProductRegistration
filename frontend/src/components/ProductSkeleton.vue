<template>
  <div class="product-card skeleton">
    <div class="product-info">
      <div class="skeleton-title"></div>
      <div class="skeleton-text"></div>
      <div class="skeleton-details">
        <div class="skeleton-detail"></div>
        <div class="skeleton-detail"></div>
      </div>
    </div>
    <div class="actions">
      <div class="skeleton-button"></div>
      <div class="skeleton-button"></div>
      <div class="skeleton-button"></div>
    </div>
  </div>
</template>

<style scoped>
.skeleton {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  padding: 1.5rem;
  flex: 1 1 260px;
  min-width: 260px;
  max-width: 320px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.skeleton-title {
  height: 24px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: 4px;
  margin-bottom: 0.5rem;
  width: 70%;
}

.skeleton-text {
  height: 16px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: 4px;
  margin-bottom: 1rem;
  width: 100%;
}

.skeleton-details {
  display: flex;
  justify-content: space-between;
  margin-bottom: 1rem;
}

.skeleton-detail {
  height: 20px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: 4px;
  width: 45%;
}

.actions {
  display: flex;
  gap: 0.5rem;
  margin-top: 1rem;
}

.skeleton-button {
  height: 36px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: 4px;
  flex: 1;
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
    max-width: 100%;
    min-width: 0;
  }
}
</style> 
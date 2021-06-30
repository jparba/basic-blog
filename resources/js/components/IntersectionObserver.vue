<template>
  <div class="d-flex justify-content-center">
    <div class="spinner-border text-success" role="status">
    </div>
  </div>
</template>

<script>
export default {
  props: {
    options: {
      type: Object,
      default() {
        return {}
      }
    }
  },
  data: () => ({
    observer: null,
  }),
  mounted() {
    this.observer = new IntersectionObserver(([entry]) => {
      if (entry && entry.isIntersecting) {
        this.$emit("intersect");
      }
    }, this.options)

    this.observer.observe(this.$el)
  },
  destroyed() {
    this.observer.disconnect()
  },
};
</script>
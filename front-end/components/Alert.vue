<template>
  <div>
    <div :class="`alert ${$store.state.alert.status}`">{{ $store.getters.message }}</div>
  </div>
</template>

<style lang="scss">
.alert {
  padding: 15px;
  margin: 20px 0;
  border: 1px solid transparent;
  border-radius: 4px;
  &.success {
    background-color: #dff0d8;
    border-color: #d6e9c6;
    color: #3c763d;
  }
  &.danger {
    background-color: #f2dede;
    border-color: #ebccd1;
    color: #a94442;
  }
}

.none {
  display: none;
}
</style>

<script>
export default {
  beforeUpdate() {
    console.log('updatetestttttttttttt');
    this.$store.commit('alertFlash');
  }
}
</script>
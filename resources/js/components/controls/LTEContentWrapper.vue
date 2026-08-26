<template>
  <div class="content-wrapper">

    <!-- Slot cho content-header -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2" v-if="title">
          <div class="col-sm-6">
            <h1 style="font-size: 25px;" class="text-blue">
              {{ title }}
            </h1>
          </div>
        </div>
      </div>
    </div>

    <!-- Slot cho content -->
    <section class="content">
      <div class="container-fluid">
        <slot name="content"></slot>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  props: {
    title: String,
  }
}
</script>

<style scoped>

</style>
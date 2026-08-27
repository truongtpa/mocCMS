<template>
  <nav>
    <ol class="breadcrumb">
      <li v-for="(item, index) in items" :key="index" class="breadcrumb-item">
        <router-link v-if="index < items.length - 1" :to="item.link">{{ item.text }}</router-link>
        <span v-else>{{ item.text }}</span>
      </li>
    </ol>
  </nav>
</template>

<script>
export default {
  name: 'BreadCrumb',
  props: {
    items: {
      type: Array,
      required: true
    }
  }
}
</script>

<style scoped>
.breadcrumb {
    list-style: none;
    display: flex;
    background-color: transparent;
    margin-bottom: 0;
    padding: 0;
}

.breadcrumb-item {
    padding-left: 0;
    margin-right: 0;
}

/* Ghi đè separator mặc định */
.breadcrumb-item + .breadcrumb-item::before {
    content: '>';
    margin: 0;
    padding-left: 8px;
    color: #6c757d; /* optional: giống bootstrap */
}

/* Không thêm gì sau item cuối */
.breadcrumb-item:last-child::after {
    content: '';
}
</style>

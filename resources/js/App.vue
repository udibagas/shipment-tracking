<template>
	<component :is="layout"><router-view /></component>
</template>

<script>
const default_layout = "app";

import { mapActions } from "vuex";

export default {
	computed: {
		layout() {
			return (this.$route.meta.layout || default_layout) + "-layout";
		}
	},

	methods: {
		...mapActions({
			me: "auth/me"
		})
	},

	mounted() {
		this.me();
	}
};
</script>

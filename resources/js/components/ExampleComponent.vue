<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <template>
                    <div>
                        <v-file-input chips multiple label="File input w/ chips"></v-file-input>
                        <v-file-input small-chips multiple label="File input w/ small chips"></v-file-input>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>

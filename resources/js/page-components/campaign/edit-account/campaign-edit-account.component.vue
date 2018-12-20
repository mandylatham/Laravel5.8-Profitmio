<template>
    <form action="" name="form-edit-detail">
        <div class="container-fluid edit-campaign-detail--container">
            <div class="row ">
                <div class="col-12 col-md-6">
                    <div class="form-group pm-form-group">
                        <label>Agency</label>
                        <v-select :options="agencies" class="filter--v-select"></v-select>
                    </div>
                    <div class="form-group pm-form-group">
                        <label>Client</label>
                        <v-select :options="clients" class="filter--v-select"></v-select>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>
<script>
    require("./campaign-edit-account.scss");
    export default {
        data() {
            return {
                agencies: [],
                clients: []
            }
        }
    }
</script>

<template>
    <form action="" name="form-edit-detail">
        <div class="container-fluid edit-campaign-detail--container">
            <div class="row ">
                <div class="col-12 col-md-6">
                    <div class="form-group pm-form-group">
                        <label for="name">Campaign Name</label>
                        <input type="text" class="form-control" id="name" name="name">
                    </div>
                    <div class="form-group pm-form-group">
                        <label for="order">Order Number</label>
                        <input type="text" class="form-control" id="order" name="order">
                    </div>
                    <div class="form-group pm-form-group">
                        <label>Status</label>
                        <v-select :options="statues" class="filter--v-select"></v-select>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group pm-form-group">
                        <label>Start Date</label>
                        <datepicker :append-to-body="true" v-model="time1" :lang="'en'" class="pm-datepicker">
                            <template slot="calendar-icon">
                                <calendar-icon></calendar-icon>
                            </template>
                        </datepicker>
                    </div>
                    <div class="form-group pm-form-group">
                        <label>End Date</label>
                        <datepicker :append-to-body="true" v-model="time1" :lang="'en'" class="pm-datepicker">
                            <template slot="calendar-icon">
                                <calendar-icon></calendar-icon>
                            </template>
                        </datepicker>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>
<script>
    require("./campaign-edit-detail.scss");
    export default {
        data() {
            return {
                time1: null,
                statues: []
            }
        }
    }
</script>

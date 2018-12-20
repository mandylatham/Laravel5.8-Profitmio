<template>
    <div class="container-fluid edit-campaign-phone--container">
        <div class="row ">
            <div class="col-12">
                <button class="btn btn-pm">
                    <plus-icon></plus-icon>
                    <span>ADD PHONE NUMBER</span>
                </button>
            </div>
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-condensed table-transparent">
                        <thead>
                        <tr>
                            <td>Number</td>
                            <td>Forward</td>
                            <td>Call Source</td>
                            <td></td>
                        </tr>
                        </thead>
                        <tbody>
                            <td>
                                <input type="text" class="form-control">
                            </td>
                            <td>
                                <input type="text" class="form-control">
                            </td>
                            <td>
                                <input type="text" class="form-control">
                            </td>
                            <td>
                                <a class="btn btn-pm">
                                    <edit-icon></edit-icon>
                                </a>
                            </td>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    require("./campaign-edit-phone.scss");
    export default {
        data() {
            return {
            }
        }
    }
</script>

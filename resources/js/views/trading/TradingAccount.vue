<template>
    <div>
        <page-title-bar></page-title-bar>
        <app-section-loader :status="accounts_loading"></app-section-loader>
        <v-container fluid class="grid-list-xl pt-0 mt-n3">
            <v-row>
                <app-card :fullBlock="true" colClasses="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <v-row class="align-items-center search-wrap">
                        <v-col cols="12" md="12" lg="12" class="align-items-center pt-0">
                            <app-card customClasses="mb-0 pt-8">
                                <v-row>
                                    <v-col cols="12" md="12" lg="12" class="pb-0">
                                        <div class="d-flex ">
                                            <div class="w-75">
                                                <v-text-field class=" pt-0 pr-3" label="Search Accounts">
                                                </v-text-field>
                                            </div>
                                            <div>
                                                <v-btn color="primary" class="my-0 ml-0 mr-2" medium><i
                                                        class="material-icons">search</i>&nbsp;&nbsp;Search</v-btn>
                                            </div>
                                        </div>
                                    </v-col>
                                </v-row>
                            </app-card>
                        </v-col>
                    </v-row>
                    <v-data-table :headers="headers" :items="accounts_data" :search="search" item-key="email"
                        :server-items-length="accounts_total" :options.sync="options" :loading="accounts_loading"
                        :footer-props="{showFirstLastPage: true,}" :items-per-page-options="[5, 10, 15, 20, -1]">
                        <template slot="headerCell" slot-scope="props" :loading-text="'Loading... Please wait'">
                            <v-tooltip bottom>
                                <span slot="activator">
                                    {{ props.header.text }}
                                </span>
                                <span>
                                    {{ props.header.text }}
                                </span>
                            </v-tooltip>
                        </template>
                        <template v-slot:item="props">
                            <tr>
                                <td>{{ props.index + 1 }}</td>
                                <td>{{ props.item.account_number }}</td>
                                <td>{{ props.item.broker }}</td>
                                <td>
                                    <v-badge :value=false :class="props.item.statusColor">{{ props.item.status }}
                                    </v-badge>
                                </td>
                                <td>{{ getDateFormat(props.item.created_at) }}</td>
                                <td>
                                    <router-link v-if="props.item.status == 'PROVIDE'"
                                        :to="{ name: 'signal-detail', params: { account_number: props.item.account_number, broker: props.item.broker } }">
                                        <v-tooltip bottom>
                                            <template v-slot:activator="{ on }">
                                                <v-btn v-on="on" icon color="primary">
                                                    <v-icon class="zmdi zmdi-eye"></v-icon>
                                                </v-btn>
                                            </template>
                                            <span>View This account</span>
                                        </v-tooltip>
                                    </router-link>

                                    <router-link v-if="props.item.status == 'COPY'"
                                        :to="{ name: 'copying-signal', hash: '#' + props.item.account_number }">
                                        <v-tooltip bottom>
                                            <template v-slot:activator="{ on }">
                                                <v-btn v-on="on" icon color="primary">
                                                    <v-icon class="zmdi zmdi-eye"></v-icon>
                                                </v-btn>
                                            </template>
                                            <span>View This account</span>
                                        </v-tooltip>
                                    </router-link>
                                    <v-tooltip bottom>
                                        <template v-slot:activator="{ on }">
                                            <v-btn v-on="on" v-if="props.item.status == 'COPY'" icon color="success"
                                                @click="changeSetting(props.item.account_number, props.item.broker)">
                                                <v-icon class="zmdi zmdi-settings"></v-icon>
                                            </v-btn>
                                        </template>
                                        <span>Change Copy Settings</span>
                                    </v-tooltip>
                                </td>
                            </tr>
                        </template>
                    </v-data-table>
                </app-card>
                <v-dialog v-model="settingModal" max-width="800">
                    <v-form v-if="form && !isLoadingSettings" ref="form" lazy-validation>
                        <template>
                            <v-card class="mx-auto px-6">
                                <v-card-title class="title font-weight-regular justify-space-between">
                                    <span>Copy Settings</span>
                                    <v-avatar color="primary lighten-2" class="subheading white--text" size="24"
                                        v-text="formstep"></v-avatar>
                                </v-card-title>
                                <v-divider></v-divider>

                                <v-window v-model="formstep">
                                    <v-window-item :value="1">
                                        <v-row>
                                            <v-col class="py-0">
                                                <v-checkbox v-model="form.isOpenTradesInDestination"
                                                    label="Open Trades in Destination?">
                                                </v-checkbox>
                                            </v-col>
                                            <v-col class="py-0">
                                                <v-checkbox v-model="form.isOpenPendingOrdersInDestination"
                                                    label="Open Pending Orders in Destination?">
                                                </v-checkbox>
                                            </v-col>
                                        </v-row>
                                        <v-row>
                                            <v-col class="py-0">
                                                <v-select v-model="form.copyDirection" :items="copyDirections"
                                                    label="Copy Direction">
                                                </v-select>
                                            </v-col>
                                            <v-col class="py-0">
                                                <v-checkbox v-model="form.isCloseTradesInDestination"
                                                    label="Close Trades in Destination?">
                                                </v-checkbox>
                                            </v-col>
                                        </v-row>
                                        <v-row>
                                            <v-col class="py-0">
                                                <v-checkbox v-model="form.isDeletePendingOrdersInDestination"
                                                    label="Delete Pending Orders in Destination?">
                                                </v-checkbox>
                                            </v-col>
                                            <v-col class="py-0">
                                                <v-checkbox v-model="form.isInvertTradeCopyDirection"
                                                    label="Invert Trade Copy Direction?">
                                                </v-checkbox>
                                            </v-col>
                                        </v-row>
                                        <v-row>
                                            <v-col class="py-0">
                                                <v-checkbox v-model="form.isCopyTPToDestination"
                                                    label="Copy TP To Destination?">
                                                </v-checkbox>
                                                <v-text-field v-model="form.overrideDestinationTP" type="number"
                                                    :disabled="!form.isCopyTPToDestination"
                                                    label="Override Destination TP (Points)">
                                                </v-text-field>
                                            </v-col>
                                            <v-col class="py-0">
                                                <v-checkbox v-model="form.isCopySLToDestination"
                                                    label="Copy SL To Destination?">
                                                </v-checkbox>
                                                <v-text-field v-model="form.overrideDestinationSL" type="number"
                                                    :disabled="!form.isCopySLToDestination"
                                                    label="Override Destination SL (Points)">
                                                </v-text-field>
                                            </v-col>
                                        </v-row>
                                    </v-window-item>

                                    <v-window-item :value="2">
                                        <v-row>
                                            <v-col>
                                                <v-select v-model="form.lotSizeType" :items="lotSizeTypes"
                                                    label="Lot Size Type">
                                                </v-select>
                                            </v-col>
                                            <v-col>
                                                <v-text-field v-model="form.lotSizeRisk" type="number"
                                                    label="Lot Size Risk %">
                                                </v-text-field>
                                            </v-col>
                                        </v-row>
                                        <v-row>
                                            <v-col class="py-0">
                                                <v-text-field v-model="form.lotSizeMultipleOfSource" type="number"
                                                    label="Lot Size Multiple Of Source">
                                                </v-text-field>
                                            </v-col>
                                            <v-col class="py-0">
                                                <v-text-field v-model="form.fixedLotSize" type="number"
                                                    label="Fixed Lot Size">
                                                </v-text-field>
                                            </v-col>
                                        </v-row>
                                        <v-row>
                                            <v-col class="py-0">
                                                <v-text-field v-model="form.minimumLotSize" type="number"
                                                    label="Minimum Lot Size">
                                                </v-text-field>
                                            </v-col>
                                            <v-col class="py-0">
                                                <v-text-field v-model="form.maximumLotSize" type="number"
                                                    label="Maximum Lot Size">
                                                </v-text-field>
                                            </v-col>
                                        </v-row>
                                    </v-window-item>

                                    <v-window-item :value="3">
                                        <v-row>
                                            <v-col>
                                                <v-text-field v-model="form.maximumOrdersInDestination" type="number"
                                                    label="Maximum Orders In Destination (0 = Unlimited)">
                                                </v-text-field>
                                            </v-col>
                                            <v-col>
                                                <v-text-field v-model="form.maximumOpenPriceSlippage" type="number"
                                                    label="Maximum Open Price Slippage (Points) (Not Supported By All Brokers)">
                                                </v-text-field>
                                            </v-col>
                                        </v-row>
                                        <v-row>
                                            <v-col class="py-0">
                                                <v-text-field v-model="form.maximumOpenPriceDeviationToCopy"
                                                    type="number" label="Maximum Open Price Deviation To Copy (Points)">
                                                </v-text-field>
                                            </v-col>
                                            <v-col class="py-0">
                                                <v-text-field v-model="form.maximumTimeAfterSourceOpen" type="number"
                                                    label="Maximum Time After Source Open (secs)">
                                                </v-text-field>
                                            </v-col>
                                        </v-row>
                                        <v-row>
                                            <v-col class="py-0">
                                                <v-text-field v-model="form.dailyProfitToStop" type="number"
                                                    label="Daily Profit % To Stop">
                                                </v-text-field>
                                            </v-col>
                                            <v-col class="py-0">
                                                <v-checkbox v-model="form.isCloseTradesWhenDailyProfitIsReached"
                                                    label="Close Trades When Daily Profit % Is Reached?">
                                                </v-checkbox>
                                            </v-col>
                                        </v-row>
                                        <v-row>
                                            <v-col class="py-0">
                                                <v-text-field v-model="form.dailyLossToStop" type="number"
                                                    label="Daily Loss % To Stop">
                                                </v-text-field>
                                            </v-col>
                                            <v-col class="py-0">
                                                <v-checkbox v-model="form.isCloseTradesWhenDailyLossIsReached"
                                                    label="Close Trades When Daily Loss % Is Reached?">
                                                </v-checkbox>
                                            </v-col>
                                        </v-row>
                                    </v-window-item>

                                    <v-window-item :value="4">
                                        <v-row>
                                            <v-col class="py-0">
                                                <v-checkbox v-model="form.isSendAlertForNewTrades"
                                                    label="Send Alert For New Trades?">
                                                </v-checkbox>
                                            </v-col>
                                            <v-col class="py-0">
                                                <v-checkbox v-model="form.isSendAlertForClosedTrades"
                                                    label="Send Alert For Closed Trades?">
                                                </v-checkbox>
                                            </v-col>
                                        </v-row>
                                        <v-row>
                                            <v-col class="py-0">
                                                <v-checkbox v-model="form.isSendAlertForPartiallyClosedTrades"
                                                    label="Send Alert For Partially Closed Trades?">
                                                </v-checkbox>
                                            </v-col>
                                            <v-col class="py-0">
                                                <v-checkbox v-model="form.isSendAlertForDailyProfitReached"
                                                    label="Send Alert For Daily Profit % Reached">
                                                </v-checkbox>
                                            </v-col>
                                        </v-row>
                                        <v-row>
                                            <v-col class="py-0">
                                                <v-checkbox v-model="form.isSendAlertForDailyLossReached"
                                                    label="Send Alert For Daily Loss % Reached">
                                                </v-checkbox>
                                            </v-col>
                                            <v-col class="py-0"></v-col>
                                        </v-row>
                                        <v-row>
                                            <v-col class="py-0">
                                                <v-checkbox v-model="form.isAlertSound" label="Alert Sound?">
                                                </v-checkbox>
                                            </v-col>
                                            <v-col class="py-0">
                                                <v-checkbox v-model="form.isAlertPopup" label="Alert Popup?">
                                                </v-checkbox>
                                            </v-col>
                                        </v-row>
                                        <v-row>
                                            <v-col class="py-0">
                                                <v-checkbox v-model="form.isAlertEmail" label="Alert Email?">
                                                </v-checkbox>
                                            </v-col>
                                            <v-col class="py-0">
                                                <v-checkbox v-model="form.isAlertMobile" label="Alert Mobile?">
                                                </v-checkbox>
                                            </v-col>
                                        </v-row>
                                    </v-window-item>

                                    <v-window-item :value="5">
                                        <v-row>
                                            <v-col cols="6">
                                                <v-select v-model="form.brokerServerSummerTimeZone"
                                                    :items="brokerServerTimeZones"
                                                    label="Broker Server Summer Time Zone">
                                                </v-select>
                                            </v-col>
                                            <v-col cols="6">
                                                <v-select v-model="form.brokerServerWinterTimeZone"
                                                    :items="brokerServerTimeZones"
                                                    label="Broker Server Winter Time Zone">
                                                </v-select>
                                            </v-col>
                                        </v-row>
                                        <v-row>
                                            <v-col class="py-0">
                                                <v-text-field v-model="form.brokerSymbolPrefix" type="text"
                                                    label="Broker Symbol Prefix">
                                                </v-text-field>
                                            </v-col>
                                            <v-col class="py-0">
                                                <v-text-field v-model="form.brokerSymbolSuffix" type="text"
                                                    label="Broker Symbol Suffix">
                                                </v-text-field>
                                            </v-col>
                                        </v-row>
                                        <v-row>
                                            <v-col class="py-0">
                                                <span>Message Color</span>
                                                <v-color-picker class="ma-2" dot-size="10" canvas-height="100"
                                                    v-model="form.messageColor"></v-color-picker>
                                            </v-col>
                                        </v-row>
                                    </v-window-item>

                                    <v-window-item :value="6">
                                        <v-row>
                                            <v-col class="py-0">
                                                <v-checkbox v-model="form.applyTrailingStop" label="ApplyTrailingStop">
                                                </v-checkbox>
                                            </v-col>
                                            <v-col class="py-0">
                                                <v-checkbox v-model="form.profitTrailing" label="ProfitTrailing">
                                                </v-checkbox>
                                            </v-col>
                                        </v-row>
                                        <v-row>
                                            <v-col class="py-0">
                                                <v-text-field v-model="form.trailingStop" type="number"
                                                    label="TrailingStop">
                                                </v-text-field>
                                            </v-col>
                                            <v-col class="py-0">
                                                <v-text-field v-model="form.trailingStep" type="number"
                                                    label="TrailingStep">
                                                </v-text-field>
                                            </v-col>
                                        </v-row>
                                        <v-row>
                                            <v-col class="py-0">
                                                <v-checkbox v-model="form.applyOnOffTime" label="ApplyOnOffTime">
                                                </v-checkbox>
                                            </v-col>
                                            <v-col class="py-0"></v-col>
                                        </v-row>
                                        <v-expand-transition>
                                            <v-row v-show="form.applyOnOffTime">
                                                <v-col class="py-0">
                                                    <v-dialog ref="onTimeDialogRef" v-model="onTimeDialog"
                                                        :return-value.sync="form.onTime" persistent width="290px">
                                                        <template v-slot:activator="{ on, attrs }">
                                                            <v-text-field v-model="form.onTime" label="On Time"
                                                                prepend-icon="mdi-clock-time-four-outline" readonly
                                                                v-bind="attrs" v-on="on"></v-text-field>
                                                        </template>
                                                        <v-time-picker v-if="onTimeDialog" v-model="form.onTime"
                                                            full-width>
                                                            <v-spacer></v-spacer>
                                                            <v-btn text color="primary" @click="onTimeDialog = false">
                                                                Cancel
                                                            </v-btn>
                                                            <v-btn text color="primary"
                                                                @click="$refs.onTimeDialogRef.save(form.onTime)">
                                                                OK
                                                            </v-btn>
                                                        </v-time-picker>
                                                    </v-dialog>
                                                </v-col>
                                                <v-col class="py-0">
                                                    <v-dialog ref="offTimeDialogRef" v-model="offTimeDialog"
                                                        :return-value.sync="form.offTime" persistent width="290px">
                                                        <template v-slot:activator="{ on, attrs }">
                                                            <v-text-field v-model="form.offTime" label="Off Time"
                                                                prepend-icon="mdi-clock-time-four-outline" readonly
                                                                v-bind="attrs" v-on="on"></v-text-field>
                                                        </template>
                                                        <v-time-picker v-if="offTimeDialog" v-model="form.offTime"
                                                            full-width>
                                                            <v-spacer></v-spacer>
                                                            <v-btn text color="primary" @click="offTimeDialog = false">
                                                                Cancel
                                                            </v-btn>
                                                            <v-btn text color="primary"
                                                                @click="$refs.offTimeDialogRef.save(form.offTime)">
                                                                OK
                                                            </v-btn>
                                                        </v-time-picker>
                                                    </v-dialog>
                                                </v-col>
                                            </v-row>
                                        </v-expand-transition>
                                    </v-window-item>

                                    <v-window-item :value="7">
                                        <v-row>
                                            <v-col>
                                                <v-checkbox v-model="form.applyDestinationPair"
                                                    label="ApplyDestinationPair">
                                                </v-checkbox>
                                            </v-col>
                                            <v-col>
                                                <v-text-field v-model="form.destinationPair" label="DestinationPair">
                                                </v-text-field>
                                            </v-col>
                                        </v-row>
                                        <v-row>
                                            <v-col class="py-0">
                                                <v-checkbox v-model="form.applyMessageLog" label="ApplyMessageLog">
                                                </v-checkbox>
                                            </v-col>
                                            <v-col class="py-0">
                                                <v-checkbox v-model="form.applyOrderCloseBy" label="ApplyOrderCloseBy">
                                                </v-checkbox>
                                            </v-col>
                                        </v-row>
                                    </v-window-item>
                                </v-window>

                                <v-divider></v-divider>

                                <v-card-actions>
                                    <v-btn :disabled="formstep === 1" text @click="formstep--">
                                        Back
                                    </v-btn>
                                    <v-spacer></v-spacer>
                                    <v-btn v-if="formstep === 7" :disabled="isFormSubmitting" color="primary" depressed
                                        @click="saveSettings">
                                        Submit
                                    </v-btn>
                                    <v-btn v-else="formstep === 7" :disabled="checkNextisDisabled" color="primary"
                                        depressed @click="nextStep">
                                        Next
                                    </v-btn>
                                </v-card-actions>
                            </v-card>
                        </template>
                    </v-form>
                    <template v-if="isLoadingSettings">
                        <v-card class="mx-auto px-6" style="height: 400px;">
                            <v-row class="fill-height" align-content="center" justify="center">
                                <v-col class="title-1 text-center" cols="12">
                                    Getting your settings
                                </v-col>
                                <v-col cols="6">
                                    <v-progress-linear color="deep-purple accent-4" indeterminate rounded height="6">
                                    </v-progress-linear>
                                </v-col>
                            </v-row>
                        </v-card>
                    </template>
                    <template v-if="!form && !isLoadingSettings">
                        <v-card class="mx-auto px-6" style="height: 400px;">
                            <v-row class="fill-height" align-content="center" justify="center">
                                <v-col class="title-1 text-center" cols="12">
                                    Loading Failed. Please try again later.
                                </v-col>
                            </v-row>
                        </v-card>
                    </template>
                </v-dialog>
            </v-row>
        </v-container>
    </div>
</template>

<script>
    import { mapGetters, mapActions } from "vuex";
    import dateformat from 'dateformat';
    import axios from "axios";
    import webServices from "WebServices";
    import Vue from "vue";
    import Nprogress from 'nprogress';

    const defaultFormValue = {
        isOpenTradesInDestination: true,
        isOpenPendingOrdersInDestination: true,
        copyDirection: 2,
        isCopyTPToDestination: true,
        overrideDestinationTP: 0,
        isCopySLToDestination: true,
        overrideDestinationSL: 0,
        isCloseTradesInDestination: true,
        isDeletePendingOrdersInDestination: true,
        isInvertTradeCopyDirection: false,
        lotSizeType: 0,
        lotSizeRisk: 0.5,
        lotSizeMultipleOfSource: 1,
        fixedLotSize: 0.01,
        minimumLotSize: 0.01,
        maximumLotSize: 100,
        maximumOrdersInDestination: 0,
        maximumOpenPriceSlippage: 0,
        maximumOpenPriceDeviationToCopy: 0,
        maximumTimeAfterSourceOpen: 0,
        dailyProfitToStop: 0,
        isCloseTradesWhenDailyProfitIsReached: false,
        dailyLossToStop: 0,
        isCloseTradesWhenDailyLossIsReached: false,
        isSendAlertForNewTrades: false,
        isSendAlertForClosedTrades: false,
        isSendAlertForPartiallyClosedTrades: false,
        isSendAlertForDailyProfitReached: false,
        isSendAlertForDailyLossReached: false,
        isAlertSound: false,
        isAlertPopup: false,
        isAlertEmail: false,
        isAlertMobile: false,
        brokerServerSummerTimeZone: 1,
        brokerServerWinterTimeZone: 2,
        brokerSymbolPrefix: '',
        brokerSymbolSuffix: '',
        messageColor: '#000000',
        applyTrailingStop: false,
        profitTrailing: true,
        trailingStop: 8,
        trailingStep: 2,
        applyOnOffTime: false,
        onTime: "02:00",
        offTime: "17:30",
        applyDestinationPair: false,
        destinationPair: 'EURUSD',
        applyMessageLog: true,
        applyOrderCloseBy: false,
    };

    export default {
        data() {
            return {
                search: "",
                headers: [
                    {
                        text: "#",
                        align: "left",
                        sortable: false,
                        value: 'id'
                    },
                    { text: "Account Number", value: "account_number", sortable: false },
                    { text: "Broker", value: "account_number", sortable: false },
                    { text: "Status", value: "online_status", sortable: false },
                    { text: "Created At", value: "created_at", sortable: false },
                    { text: "", value: "actions", sortable: false },
                ],
                copyDirections: [
                    { text: 'Only Buy', value: 1 },
                    { text: 'Only Sell', value: -1 },
                    { text: 'Buy & Sell', value: 2 },
                ],
                lotSizeTypes: [
                    { text: "Risk % Of Balance", value: 0 },
                    { text: "Risk % Of Balance(Including Margin Required)", value: 3 },
                    { text: "Multiple Of Source", value: 1 },
                    { text: "Fixed", value: 2 }
                ],
                brokerServerTimeZones: [
                    { text: 'EEST', value: 1 },
                    { text: 'EET', value: 2 },
                    { text: 'AEST', value: 3 },
                    { text: 'AEDT', value: 4 },
                    { text: 'EST', value: 5 },
                    { text: 'EDT', value: 6 },
                    { text: 'JST', value: 7 },
                ],
                options: {},
                isLoadingSettings: false,
                selected_account: null,
                formstep: 1,
                onTimeDialog: false,
                offTimeDialog: false,
                settingModal: false,
                isFormSubmitting: false,
                form: null,
            };
        },
        mounted() {
        },
        methods: {
            ...mapActions([
                'getAccountsAction'
            ]),
            ...{
                getDateFormat(date) {
                    if (!date) return '';
                    return dateformat(new Date(date), "mmm, dd yyyy HH:MM")
                },
                getDateFormatWithMS(date) {
                    if (!date) return '';
                    date = parseInt(date);
                    return dateformat(date, "mmm, dd yyyy HH:MM")
                },
                saveSettings() {
                    this.isFormSubmitting = true;
                    Nprogress.start();
                    axios.post(`${webServices.baseURL}/savecopysetting`, { ...this.form, ...this.selected_account })
                        .then(({ data }) => {
                            Vue.notify({
                                group: 'signals',
                                type: 'success',
                                text: 'Setting saved!'
                            });
                            this.selected_account = null;
                            this.settingModal = false;
                        })
                        .catch(() => {
                            Vue.notify({
                                group: 'signals',
                                type: 'error',
                                text: 'Can\'t save setting'
                            });
                        })
                        .finally(() => {
                            this.isFormSubmitting = false;
                            Nprogress.done();
                        })
                },
                nextStep() {
                    this.formstep++
                },
                changeSetting(account_number, broker) {
                    this.settingModal = true;
                    this.isLoadingSettings = true;
                    this.formstep = 1,
                        this.selected_account = {
                            account_number,
                            broker
                        };
                    Nprogress.start();
                    axios.post(`${webServices.baseURL}/copysetting`, this.selected_account)
                        .then(({ data }) => {
                            const { response } = data;
                            this.form = response.setting;
                        })
                        .catch(() => {
                            this.form = null;
                        })
                        .finally(() => {
                            this.isLoadingSettings = false;
                            Nprogress.done();
                        })
                }
            }
        },
        computed: {
            ...mapGetters([
                "accounts_data",
                "accounts_perPage",
                "accounts_total",
                "accounts_page",
                "accounts_loading"
            ]),
            ... {
                checkNextisDisabled() {
                    switch (this.formstep) {
                        case 6:
                            if (!this.form.applyOnOffTime)
                                return false;
                            return (!this.form.onTime || !this.form.offTime);
                        default:
                            return false;
                    }
                },
            }
        },

        watch: {
            options: function (options) {
                this.getAccountsAction({ page: options.page, perPage: options.itemsPerPage });
            }
        }
    };
</script>
<template>
    <div>
        <page-title-bar></page-title-bar>
        <app-section-loader :status="availableSignal_loading"></app-section-loader>
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
                                                <v-text-field class=" pt-0 pr-3" label="Search Signal">
                                                </v-text-field>
                                            </div>
                                            <div>
                                                <v-btn color="primary" class="my-0 ml-0 mr-2" medium>
                                                    <i class="material-icons">search</i>&nbsp;&nbsp;Search
                                                </v-btn>
                                            </div>
                                        </div>
                                    </v-col>
                                </v-row>
                            </app-card>
                        </v-col>
                    </v-row>
                    <v-data-table :key="tableProvideKey" :headers="headers" :items="availableSignal_data"
                        :search="search" item-key="email" :server-items-length="availableSignal_total"
                        :options.sync="options" :loading="availableSignal_loading"
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
                                <td>{{ props.item.provider }}</td>
                                <td>{{ props.item.broker }}</td>
                                <td>{{ props.item.account_number }}</td>
                                <td>{{ props.item.signal_number }}</td>
                                <td>{{ getDateFormatWithMS(props.item.openTime) }}</td>
                                <td>{{ props.item.copier_number }}</td>
                                <td>
                                    <v-btn text icon color="primary"
                                        @click="gotoDetail(props.item.account_number, props.item.broker)">
                                        <v-icon class="zmdi zmdi-eye"></v-icon>
                                    </v-btn>
                                    <v-btn text icon color="success"
                                        @click="tryCopyAccount(props.item.broker, props.item.account_number)">
                                        <v-icon class="zmdi zmdi-playlist-plus"></v-icon>
                                    </v-btn>
                                </td>
                            </tr>
                        </template>
                    </v-data-table>
                </app-card>
            </v-row>
        </v-container>

        <template>
            <v-dialog v-model="copyModal" max-width="800">
                <v-form v-model="form.valid" ref="form" lazy-validation>
                    <template>
                        <v-card class="mx-auto px-6">
                            <v-card-title class="title font-weight-regular justify-space-between">
                                <span>Copy Account</span>
                                <v-avatar color="primary lighten-2" class="subheading white--text" size="24"
                                    v-text="formstep"></v-avatar>
                            </v-card-title>
                            <v-divider></v-divider>

                            <v-window v-model="formstep">
                                <v-window-item :value="1">
                                    <h3>Please select Account for copy.</h3>
                                    <v-row>
                                        <v-col cols="12" sm="12" md="5" class="py-0">
                                            <h4>
                                                Available Accounts
                                            </h4>
                                            <v-list style="height: 300px; max-height: 300px"
                                                class="overflow-y-auto border">
                                                <v-list-item-group v-model="availableaccounts_selected" multiple>
                                                    <v-list-item v-for="(account, index) in availableaccounts"
                                                        :key="index">
                                                        <template v-slot:default="{ active, }">
                                                            <v-list-item-action>
                                                                <v-checkbox :input-value="active" color="primary">
                                                                </v-checkbox>
                                                            </v-list-item-action>
                                                            <v-list-item-content>
                                                                <v-list-item-title>{{ account.account_number }}
                                                                </v-list-item-title>
                                                                <v-list-item-subtitle>{{ account.broker }}
                                                                </v-list-item-subtitle>
                                                            </v-list-item-content>
                                                        </template>
                                                    </v-list-item>
                                                </v-list-item-group>
                                            </v-list>
                                        </v-col>
                                        <v-col cols="12" sm="12" md="2" class="d-flex align-center py-0">
                                            <div>
                                                <v-btn class="mt-3" fab dark small color="pink" @click="addToCopy">
                                                    <v-icon>mdi-chevron-right</v-icon>
                                                </v-btn>
                                                <v-btn class="mt-3" fab dark small color="pink" @click="addToCopyAll">
                                                    <v-icon>mdi-chevron-double-right</v-icon>
                                                </v-btn>
                                                <v-btn class="mt-3" fab dark small color="pink" @click="removeFromCopy">
                                                    <v-icon>mdi-chevron-left</v-icon>
                                                </v-btn>
                                                <v-btn class="mt-3" fab dark small color="pink"
                                                    @click="removeFromCopyAll">
                                                    <v-icon>mdi-chevron-double-left</v-icon>
                                                </v-btn>
                                            </div>
                                        </v-col>
                                        <v-col cols="12" sm="12" md="5" class="py-0">
                                            <h4>
                                                Selected Accounts
                                            </h4>
                                            <v-list style="height: 300px; max-height: 300px"
                                                class="overflow-y-auto border">
                                                <v-list-item-group v-model="copyingaccounts_selected" multiple>
                                                    <v-list-item v-for="(account, index) in copyingaccounts"
                                                        :key="index">
                                                        <template v-slot:default="{ active, }">
                                                            <v-list-item-action>
                                                                <v-checkbox :input-value="active" color="primary">
                                                                </v-checkbox>
                                                            </v-list-item-action>
                                                            <v-list-item-content>
                                                                <v-list-item-title>{{ account.account_number }}
                                                                </v-list-item-title>
                                                                <v-list-item-subtitle>{{ account.broker }}
                                                                </v-list-item-subtitle>
                                                            </v-list-item-content>
                                                        </template>
                                                    </v-list-item>
                                                </v-list-item-group>
                                            </v-list>
                                        </v-col>
                                    </v-row>
                                </v-window-item>

                                <v-window-item :value="2">
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

                                <v-window-item :value="3">
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

                                <v-window-item :value="4">
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
                                            <v-text-field v-model="form.maximumOpenPriceDeviationToCopy" type="number"
                                                label="Maximum Open Price Deviation To Copy (Points)">
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

                                <v-window-item :value="5">
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

                                <v-window-item :value="6">
                                    <v-row>
                                        <v-col cols="6">
                                            <v-select v-model="form.brokerServerSummerTimeZone"
                                                :items="brokerServerTimeZones" label="Broker Server Summer Time Zone">
                                            </v-select>
                                        </v-col>
                                        <v-col cols="6">
                                            <v-select v-model="form.brokerServerWinterTimeZone"
                                                :items="brokerServerTimeZones" label="Broker Server Winter Time Zone">
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
                                            <v-color-picker class="ma-2" dot-size="10" hide-canvas
                                                v-model="form.messageColor"></v-color-picker>
                                        </v-col>
                                    </v-row>
                                </v-window-item>

                                <v-window-item :value="7">
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
                                                    <v-time-picker v-if="onTimeDialog" v-model="form.onTime" full-width>
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

                                <v-window-item :value="8">
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
                                <v-btn v-if="formstep === 8" :disabled="isFormSubmitting" color="primary" depressed
                                    @click="copySource">
                                    Submit
                                </v-btn>
                                <v-btn v-else="formstep === 8" :disabled="checkNextisDisabled" color="primary" depressed
                                    @click="nextStep">
                                    Next
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </template>
                </v-form>
            </v-dialog>
        </template>
    </div>
</template>

<script>
    import { mapGetters, mapActions } from "vuex";
    import dateformat from "dateformat";
    import axios from "axios";
    import webServices from "WebServices";
    import Vue from "vue";
    import Nprogress from 'nprogress';

    const defaultFormValue = {
        valid: true,
        isOpenTradesInDestination: false,
        isOpenPendingOrdersInDestination: false,
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
        maximumTimeAfterSourceOpen: 30,
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
        messageColor: '#000000FF',
        applyTrailingStop: false,
        profitTrailing: true,
        trailingStop: 8,
        trailingStep: 2,
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
                onTimeDialog: false,
                offTimeDialog: false,
                availableaccounts: [],
                copyingaccounts: [],
                availableaccounts_selected: [],
                copyingaccounts_selected: [],
                formstep: 1,
                search: "",
                headers: [
                    {
                        text: "#",
                        align: "left",
                        sortable: false,
                    },
                    { text: "Provider", sortable: false },
                    { text: "Broker", sortable: false },
                    { text: "Source Account", sortable: false },
                    { text: "Number of Signals", sortable: false },
                    { text: "Since", sortable: false },
                    { text: "Number Of Copiers", sortable: false },
                    { text: "", sortable: false },
                ],
                options: {},
                delete_id: null,
                tableProvideKey: 0,
                copyModal: false,
                source_account: null,
                accounts: [],
                copyDirections: [
                    { text: 'Only Buy', value: 1 },
                    { text: 'Only Sell', value: -1 },
                    { text: 'Buy & Sell', value: 2 },
                ],
                lotSizeTypes: [
                    { text: "Risk % Of Balance", value: 0 },
                    { text: "Risk % Of Balance(Including Margin Required)", value: 1 },
                    { text: "Multiple Of Source", value: 2 },
                    { text: "Fixed", value: 3 }
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
                form: defaultFormValue,
                isFormSubmitting: false,
            };
        },
        mounted() {
            axios.get(`${webServices.baseURL}/accounts-for-copy`, { headers: { 'Content-Type': 'application/json' } })
                .then(({ data }) => {
                    const { api_status, accounts } = data.response;
                    if (api_status) {
                        this.accounts = accounts;
                    }
                })
                .catch(() => {
                    Vue.notify({
                        group: 'signals',
                        type: 'error',
                        text: 'Loading accounts failed!'
                    });
                })
        },
        methods: {
            ...mapActions([
                'getAvailableSignalAction'
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
                gotoDetail(account_number, broker) {
                    this.$router.push({ path: `signal-detail/${account_number}/${broker}` });
                },
                tryCopyAccount(broker, account_number) {
                    this.copyModal = true;

                    this.availableaccounts = [...this.accounts];
                    this.availableaccounts_selected = [];

                    this.copyingaccounts = [];
                    this.copyingaccounts_selected = [];

                    this.source_account = { broker, account_number };

                    this.form = defaultFormValue;
                    this.formstep = 1;
                },
                addToCopy() {
                    if (this.availableaccounts_selected.length == 0) return;
                    this.availableaccounts_selected.map(selected => {
                        this.copyingaccounts.push(this.availableaccounts[selected]);
                    });

                    const newvals = this.availableaccounts.filter((account, index) => {
                        return this.availableaccounts_selected
                            .find(selected => selected == index) === undefined;
                    });
                    this.availableaccounts_selected = [];
                    this.availableaccounts = newvals;
                    this.copyingaccounts_selected = [];
                },
                addToCopyAll() {
                    this.availableaccounts = [];
                    this.availableaccounts_selected = [];
                    this.copyingaccounts = [...this.accounts];
                    this.copyingaccounts_selected = [];
                },
                removeFromCopy() {
                    if (this.copyingaccounts_selected.length == 0) return;
                    this.copyingaccounts_selected.map(selected => {
                        this.availableaccounts.push(this.copyingaccounts[selected]);
                    });

                    const newvals = this.copyingaccounts.filter((account, index) => {
                        return this.copyingaccounts_selected
                            .find(selected => selected == index) === undefined;
                    });
                    this.copyingaccounts_selected = [];
                    this.copyingaccounts = newvals;
                    this.availableaccounts_selected = [];
                },
                removeFromCopyAll() {
                    this.availableaccounts = [...this.accounts];
                    this.availableaccounts_selected = [];
                    this.copyingaccounts = [];
                    this.copyingaccounts_selected = [];
                },
                nextStep() {
                    this.formstep++
                },
                copySource() {
                    console.log(this.form);
                    // this.copyModal = false;
                    this.isFormSubmitting = true;
                    Nprogress.start();
                },
                copyConfirm() {
                    // axios.post(`${webServices.baseURL}/copysources`,
                    //     { source_account: this.source_account, account: this.form.account },
                    //     { headers: { 'Content-Type': 'application/json' } })
                    //     .then(({ data }) => {
                    //         const { api_status, message } = data.response;

                    //         if (api_status) {
                    //             this.tableProvideKey++;
                    //             Vue.notify({
                    //                 group: 'signals',
                    //                 type: 'success',
                    //                 text: 'Copying signal success!'
                    //             });
                    //         }
                    //         else {
                    //             Vue.notify({
                    //                 group: 'signals',
                    //                 type: 'error',
                    //                 text: message
                    //             });
                    //         }
                    //     })
                    //     .catch((error) => {
                    //         let message = 'Copying signal failed.';
                    //         if (error.response) {
                    //             const { response } = error.response.data;
                    //             message = response.message;
                    //         }
                    //         Vue.notify({
                    //             group: 'signals',
                    //             type: 'error',
                    //             text: message
                    //         });
                    //     })
                    //     .finally(() => {
                    //         this.copied_account = [];
                    //         this.$refs.copyConfirmationDialog.close();
                    //     });
                },
            }
        },
        computed: {
            ...mapGetters([
                "availableSignal_data",
                "availableSignal_perPage",
                "availableSignal_total",
                "availableSignal_page",
                "availableSignal_loading"
            ]),
            ...{
                checkNextisDisabled() {
                    switch (this.formstep) {
                        case 1:
                            return this.copyingaccounts.length == 0;
                        case 7:
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
                this.getAvailableSignalAction({
                    page: options.page,
                    perPage: options.itemsPerPage
                });
            }
        }
    };
</script>
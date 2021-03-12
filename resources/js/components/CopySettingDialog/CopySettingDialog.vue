<template>
    <v-dialog persistent v-if="settingType == 'copy'" v-model="settingModal" max-width="800">
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
        <v-form v-if="form && !isLoadingSettings" ref="form" lazy-validation>
            <template>
                <v-card class="mx-auto px-6">
                    <v-card-title class="title font-weight-regular justify-space-between">
                        <span>Copy Settings</span>
                        <v-avatar color="primary lighten-2" class="subheading white--text" size="24" v-text="formstep">
                        </v-avatar>
                        </v-avatar>
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
                                    <v-checkbox v-model="form.isCopyTPToDestination" label="Copy TP To Destination?">
                                    </v-checkbox>
                                    <v-text-field v-model="form.overrideDestinationTP" type="number"
                                        label="Override Destination TP (Points)">
                                    </v-text-field>
                                </v-col>
                                <v-col class="py-0">
                                    <v-checkbox v-model="form.isCopySLToDestination" label="Copy SL To Destination?">
                                    </v-checkbox>
                                    <v-text-field v-model="form.overrideDestinationSL" type="number"
                                        label="Override Destination SL (Points)">
                                    </v-text-field>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col class="py-0">
                                    <v-checkbox v-model="form.isCopyOpenTrades" label="Copy Open trades?">
                                    </v-checkbox>
                                    <v-text-field v-model="form.maxTime" type="number"
                                        :disabled="!form.isCopyOpenTrades" label="Max time (mins)">
                                    </v-text-field>
                                </v-col>
                                <v-col class="py-0">
                                    <v-text-field v-model="form.copyDelay" type="number" label="Copy Delay (seconds) ">
                                    </v-text-field>
                                </v-col>
                            </v-row>
                        </v-window-item>

                        <v-window-item :value="2">
                            <v-row>
                                <v-col>
                                    <v-select v-model="form.lotSizeType" :items="lotSizeTypes" label="Lot Size Type">
                                    </v-select>
                                </v-col>
                                <v-col>
                                    <v-text-field v-model="form.lotSizeRisk" type="number" label="Lot Size Risk %">
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
                                    <v-text-field v-model="form.fixedLotSize" type="number" label="Fixed Lot Size">
                                    </v-text-field>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col class="py-0">
                                    <v-text-field v-model="form.minimumLotSize" type="number" label="Minimum Lot Size">
                                    </v-text-field>
                                </v-col>
                                <v-col class="py-0">
                                    <v-text-field v-model="form.maximumLotSize" type="number" label="Maximum Lot Size">
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
                                    <v-select v-model="form.brokerServerSummerTimeZone" :items="brokerServerTimeZones"
                                        label="Broker Server Summer Time Zone">
                                    </v-select>
                                </v-col>
                                <v-col cols="6">
                                    <v-select v-model="form.brokerServerWinterTimeZone" :items="brokerServerTimeZones"
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
                                    <v-text-field v-model="form.trailingStop" type="number" label="TrailingStop">
                                    </v-text-field>
                                </v-col>
                                <v-col class="py-0">
                                    <v-text-field v-model="form.trailingProfit" type="number" label="TrailingProfit">
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
                                                    prepend-icon="mdi-clock-time-four-outline" readonly v-bind="attrs"
                                                    v-on="on"></v-text-field>
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
                                                    prepend-icon="mdi-clock-time-four-outline" readonly v-bind="attrs"
                                                    v-on="on"></v-text-field>
                                            </template>
                                            <v-time-picker v-if="offTimeDialog" v-model="form.offTime" full-width>
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
                                    <v-checkbox v-model="form.applyDestinationPair" label="ApplyDestinationPair">
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
                        <v-btn text @click="dismiss">
                            Cancel
                        </v-btn>
                        <v-spacer></v-spacer>
                        <v-btn :disabled="formstep === 1" text @click="prevStep" color="secondary">
                            Back
                        </v-btn>
                        <v-btn v-if="formstep === 7" :disabled="isFormSubmitting" color="success" depressed
                            @click="saveSettings">
                            Submit
                        </v-btn>
                        <v-btn v-else="formstep === 7" :disabled="checkNextisDisabled" color="primary" depressed
                            @click="nextStep">
                            Next
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </template>
        </v-form>
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

    <v-dialog v-else v-model="settingModal" max-width="800" persistent>
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
        <v-form v-if="form && !isLoadingSettings" ref="form" lazy-validation>
            <template>
                <v-card class="mx-auto px-6">
                    <v-card-title class="title font-weight-regular justify-space-between">
                        <span>Provide Settings</span>
                        <v-avatar color="primary lighten-2" class="subheading white--text" size="24" v-text="formstep">
                        </v-avatar>
                        </v-avatar>
                    </v-card-title>
                    <v-divider></v-divider>

                    <v-window v-model="formstep">
                        <v-window-item :value="1">
                            <v-row>
                                <v-col cols="6">
                                    <v-select v-model="form.brokerServerSummerTimeZone" :items="brokerServerTimeZones"
                                        label="Broker Server Summer Time Zone">
                                    </v-select>
                                </v-col>
                                <v-col cols="6">
                                    <v-select v-model="form.brokerServerWinterTimeZone" :items="brokerServerTimeZones"
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

                        <v-window-item :value="2">
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
                                    <v-text-field v-model="form.trailingStop" type="number" label="TrailingStop">
                                    </v-text-field>
                                </v-col>
                                <v-col class="py-0">
                                    <v-text-field v-model="form.trailingProfit" type="number" label="TrailingProfit">
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
                                                    prepend-icon="mdi-clock-time-four-outline" readonly v-bind="attrs"
                                                    v-on="on"></v-text-field>
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
                                                    prepend-icon="mdi-clock-time-four-outline" readonly v-bind="attrs"
                                                    v-on="on"></v-text-field>
                                            </template>
                                            <v-time-picker v-if="offTimeDialog" v-model="form.offTime" full-width>
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
                            <v-row>
                                <v-col class="py-0">
                                    <v-checkbox v-model="form.applyMessageLog" label="ApplyMessageLog">
                                    </v-checkbox>
                                </v-col>
                                <v-col class="py-0"></v-col>
                            </v-row>
                        </v-window-item>
                    </v-window>
                    <v-divider></v-divider>
                    <v-card-actions>
                        <v-btn text @click="dismiss">
                            Cancel
                        </v-btn>
                        <v-spacer></v-spacer>
                        <v-btn :disabled="formstep === 1" text @click="prevStep" color="deep-purple">
                            Back
                        </v-btn>
                        <v-btn v-if="formstep === 2" :disabled="isFormSubmitting" color="success" depressed
                            @click="saveSettings">
                            Submit
                        </v-btn>
                        <v-btn v-else="formstep === 2" :disabled="checkNextisDisabled" color="primary" depressed
                            @click="nextStep">
                            Next
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </template>
        </v-form>
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
</template>

<script>
    export default {
        props: [
            "settingType",
            "dismiss",
            "settingModal",
            "form",
            "formstep",
            "isLoadingSettings",
            "isFormSubmitting",
            "checkNextisDisabled",
            "nextStep",
            "prevStep",
            "saveSettings"
        ],
        data() {
            return {
                onTimeDialog: false,
                offTimeDialog: false,
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
            }
        }
    };
</script>
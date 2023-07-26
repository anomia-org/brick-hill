<template>
    <div>
        <div class="col-4-5">
            <div class="header-3">Value Summary</div>
        </div>
        <div class="col-1-5">
            <select class="width-100 blend" v-model="timeFrame">
                <option value="7">7 Days</option>
                <option value="30">30 Days</option>
                <option value="90">90 Days</option>
            </select>
        </div>
        <div ref="chartYColor" class="chart-y"></div>
        <div ref="chartXColor" class="chart-x"></div>
        <div v-show="hasChartData" class="mb-20" style="height: 220px">
            <canvas ref="chartRef" style="width: 100%; height: 200px"></canvas>
        </div>
        <div v-show="!hasChartData" class="text-center mb-20">
            No sales data for time frame
        </div>
    </div>
</template>

<script setup lang="ts">
import { BH } from "@/logic/bh";
import axios from "axios";
import {
    Chart,
    Legend,
    BarController,
    LineController,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    Tooltip,
    ChartConfiguration,
} from "chart.js";
import { onMounted, ref, watch } from "vue";

const props = defineProps<{
    itemId: number;
}>();

const chartXColor = ref<HTMLDivElement>();
const chartYColor = ref<HTMLDivElement>();
const chartRef = ref<HTMLCanvasElement>();
const timeFrame = ref<number>(90);

// Amount of space volume takes up on the chart
// 5 = 1/5
const volumeRatio = 4;

// Determines if testing data should be used
const useTestData = false;

// color is set in onMounted as chartRef must exist
Chart.defaults.font.family = `"Montserrat", sans-serif`;
Chart.defaults.font.weight = "600";

const chartGridColors = {
    x: "#FFFFFF11",
    y: "#FFFFFF88",
};

const yTickSet = new Set();
const yTickConfig = {
    count: 5,
    // remove decimals from the labels
    // technically makes them incorrect but doesnt need to  be 100% accurate
    callback: (label: string | number) => {
        let num = Math.round(Number(label));

        if (yTickSet.has(num)) return;
        yTickSet.add(num);

        return num;
    },
};

onMounted(() => {
    Chart.register(
        Legend,
        BarController,
        LineController,
        CategoryScale,
        LinearScale,
        PointElement,
        LineElement,
        BarElement,
        Tooltip
    );

    if (
        typeof chartRef.value === "undefined" ||
        typeof chartXColor.value === "undefined" ||
        typeof chartYColor.value === "undefined"
    )
        return;

    Chart.defaults.color = window
        .getComputedStyle(chartRef.value)
        .getPropertyValue("color");

    // we need dynamic grid colors for themes, chartjs cant just read text colors
    // i dont want to do any weird color parsing and alpha modifications
    // i have no guarantee getComputedStyle will return in rgb format for every browser anyway
    // so instead, create 2 "virtual" elements with classes that have the proper grid colors
    // and then read their colors from the browser to get from themes.scss over to chartjs
    // the only downside with this method is that if i ever add theme changing without page reloading this will break
    // as it doesnt modify the color, but would be a simpleish fix
    chartGridColors.x = window
        .getComputedStyle(chartXColor.value)
        .getPropertyValue("color");
    chartGridColors.y = window
        .getComputedStyle(chartYColor.value)
        .getPropertyValue("color");

    load();
});

type ChartData = {
    [key: string]:
        | {
              avg: number;
              count: number;
          }
        | undefined;
};

let chart: Chart;
let fullChartData: ChartData = {};
let chartData: ChartData = {};
const hasChartData = ref<boolean>(true);

watch(timeFrame, () => {
    setChartData();
});

function load() {
    axios.get(BH.apiUrl(`v1/shop/${props.itemId}/chart`)).then(({ data }) => {
        fullChartData = data.data;

        initChart();

        setChartData();
    });
}

function setChartData() {
    let pastDates = lastXDays(timeFrame.value);
    let mapToUndefined: ChartData = {};
    hasChartData.value = false || useTestData;
    for (let date of pastDates) {
        if (typeof fullChartData[date] !== "undefined") {
            hasChartData.value = true;
        }
        mapToUndefined[date] = fullChartData[date];
    }

    chartData = mapToUndefined;

    let recentPriceData = Object.keys(chartData).map(
        (data) => chartData[data]?.avg ?? NaN
    );
    let dailyVolumeData = Object.keys(chartData).map(
        (data) => chartData[data]?.count ?? NaN
    );

    if (useTestData) {
        let recentPriceTestData = [150, 100, 300, 250, 100, 120, 600];
        let dailyVolumeTestData = [1, 5, 6, 10, 0, 1, 5];

        recentPriceData = recentPriceTestData;
        dailyVolumeData = dailyVolumeTestData;
    }

    let max = Math.max(...recentPriceData.filter((num) => !isNaN(num)));
    let min = Math.min(...recentPriceData.filter((num) => !isNaN(num)));

    chart.data.labels = Object.keys(chartData);
    chart.data.datasets[0].data = recentPriceData;
    chart.data.datasets[1].data = dailyVolumeData;

    // expect errors as chartjs types cry about being undefined here
    // @ts-expect-error
    chart.options.scales.volumeAxis.suggestedMax =
        Math.max(
            ...dailyVolumeData.filter((num) => !isNaN(num)),
            5 / volumeRatio
        ) * volumeRatio;
    // @ts-expect-error
    chart.options.scales.priceAxis.suggestedMax = Math.max(max * 1.2, 5);
    // @ts-expect-error
    chart.options.scales.priceAxis.suggestedMin = min / 2;

    chart.update();
}

function initChart() {
    let config: ChartConfiguration = {
        type: "line",
        data: {
            labels: Object.keys(chartData),
            datasets: [
                {
                    type: "line",
                    label: "Recent Price",
                    pointRadius: 1,
                    pointHitRadius: 10,
                    data: [],
                    backgroundColor: "#ffd52d",
                    borderColor: "#ffd52d",
                    borderWidth: 2,
                    yAxisID: "priceAxis",
                },
                {
                    type: "bar",
                    label: "Daily Sales Volume",
                    data: [],
                    backgroundColor: "#04a9fe",
                    borderColor: "#04a9fe",
                    borderWidth: 0,
                    maxBarThickness: 15,
                    yAxisID: "volumeAxis",
                },
            ],
        },
        options: {
            plugins: {
                legend: {
                    display: true,
                    align: "start",
                    labels: {
                        boxWidth: 10,
                        boxHeight: 5,
                        textAlign: "right",
                    },
                },
            },
            scales: {
                volumeAxis: {
                    type: "linear",
                    position: "right",
                    grid: {
                        drawBorder: false,
                        display: false,
                    },
                    ticks: {
                        ...yTickConfig,
                    },
                    beforeUpdate: () => yTickSet.clear(),
                },
                priceAxis: {
                    type: "linear",
                    position: "left",
                    grid: {
                        drawBorder: false,
                        color: chartGridColors.y,
                    },
                    ticks: {
                        ...yTickConfig,
                    },
                    beforeUpdate: () => yTickSet.clear(),
                },
                x: {
                    grid: {
                        drawBorder: false,
                        color: chartGridColors.x,
                        offset: false,
                    },
                    ticks: {
                        // add leading 0 to date label
                        // this could be done in the lastXDays function but that would require mapping in backend too
                        callback: function (labelIndex: string | number) {
                            let label = this.getLabelForValue(
                                Number(labelIndex)
                            );
                            let dates = label.split("-");
                            dates = dates.map((date: string) =>
                                date.padStart(2, "0")
                            );
                            return dates.join("-");
                        },
                    },
                },
            },
            animation: {
                duration: 0,
            },
            elements: {
                line: {
                    tension: 0.01,
                    spanGaps: true,
                },
            },
        },
    };
    if (!chartRef.value) return;

    chart = new Chart(chartRef.value, config);
}

function lastXDays(dayCount: number) {
    let days = [];

    for (let i = 0; i < dayCount; i++) {
        // create new date, subtract i days from it
        let date = new Date();
        date.setUTCDate(new Date().getUTCDate() - i);
        days.push(`${date.getUTCMonth() + 1}-${date.getUTCDate()}`);
    }

    return days.reverse();
}
</script>

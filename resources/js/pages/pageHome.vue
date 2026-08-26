<template>
    <LTEContentWrapper>
        <template #content>
            <div class="row">
                <div class="col-xl-6 col-lg-6 col-md-12 col-12">
                    <router-link :to="{ name: 'router-vanban' }"
                                 class="card-link">
                                                <LTECard :title="`Tổng số văn bản (Tháng ${getCurrentMonth()})`" class="card-gradient card-blue cursor-pointer">
                                                    <template #content>
                                                        <h3 class="text-center text-white">{{ thongKe.tong_so_van_ban_thang }}</h3>
                                                        <p class="text-center text-white-50">Tổng số văn bản trong hệ thống</p>
                                                    </template>
                                                </LTECard>
                    </router-link>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-12 col-12">
                    <router-link
                                 :to="{ name: 'router-thongbao' }" class="card-link">
                        <LTECard title="Thông báo mới (Tuần này)" class="card-gradient card-purple cursor-pointer">
                            <template #content>
                                <h3 class="text-center text-white">{{ thongKe.tong_so_thong_bao_tuan }}</h3>
                                <p class="text-center text-white-50">Thông báo thêm trong tuần</p>
                            </template>
                        </LTECard>
                    </router-link>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-12 col-12">
                    <router-link
                                 :to="{ name: 'router-adminlichcongtac' }" class="card-link">
                        <LTECard title="Lịch công tác mới (Tuần này)" class="card-gradient card-green cursor-pointer">
                            <template #content>
                                <h3 class="text-center text-white">{{ thongKe.tong_so_lich_cong_tac_tuan }}</h3>
                                <p class="text-center text-white-50">Lịch công tác thêm trong tuần</p>
                            </template>
                        </LTECard>
                    </router-link>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-12 col-12">
                    <router-link :to="{ name: 'router-danhmucbaocaonoibo' }"
                                 class="card-link">
                        <LTECard :title="`Tổng số báo cáo (Tháng ${getCurrentMonth()})`" class="card-gradient card-orange cursor-pointer">
                            <template #content>
                                <h3 class="text-center text-white">{{ thongKe.tong_so_bao_cao_thang }}</h3>
                                <p class="text-center text-white-50">Tổng số báo cáo trong tháng</p>
                            </template>
                        </LTECard>
                    </router-link>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <LTECard title="Số lượng văn bản theo loại">
                        <template #content>
                            <canvas id="vanBanChart" height="300"></canvas>
                        </template>
                    </LTECard>
                </div>
            </div>
        </template>
    </LTEContentWrapper>
</template>

<script>
import LTEContentWrapper from "@/components/controls/LTEContentWrapper.vue";
import LTECard from "@/components/controls/LTECard.vue";
import Chart from 'chart.js/auto';

export default {
    components: {
        LTEContentWrapper,
        LTECard,
    },
    data() {
        return {
            thongKe: {
                tong_so_van_ban_thang: 0,
                van_ban_theo_loai: [],
                tong_so_lich_cong_tac_tuan: 0,
                tong_so_thong_bao_tuan: 0,
                tong_so_bao_cao_thang: 0
            },
            colorPalette: ['#165F47', '#2DA049','#AED140','#E3EA98','#F6C817','#F29238','#B91F3F','#8C4074'],
            chart: null,
            __tttk: __tttk, // Assuming __tttk is globally available as in sidebar
        };
    },
    mounted() {
        this.loadThongKe();

    },
    methods: {
        getCurrentYear() {
            return new Date().getFullYear()
        },
        getCurrentMonth() {
            return new Date().getMonth() + 1
        },
        async loadThongKe() {
            const response = await this.$axios.get(route('VanBanController.thongKe'));
            if (response.data.status === 200) {
                this.thongKe = response.data.data;
                this.renderChart();
                // this.renderVanBanChart();
            } else {
                this.$func.toastError(response.data);
            }
        },
        renderChart() {
            const ctx = document.getElementById('vanBanChart').getContext('2d');

            const months = Array.from({ length: 12 }, (_, i) => `Tháng ${i + 1}`);
            const vanBanTheoThang = this.thongKe.van_ban_theo_loai;

            // Step 1: Get all unique document types
            const typeSet = new Set();
            vanBanTheoThang.forEach(monthData => {
                monthData.data.forEach(vb => {
                    typeSet.add(vb.ten_loai_van_ban);
                });
            });
            const docTypes = Array.from(typeSet);

            // Step 2: Build datasets (one for each document type)
            const datasets = docTypes.map((typeName, idx) => {
                const backgroundColor = this.colorPalette[idx % this.colorPalette.length];
                const borderColor = this.colorPalette[idx % this.colorPalette.length];

                const data = months.map((_, monthIndex) => {
                    const monthData = vanBanTheoThang.find(d => d.thang === monthIndex + 1);
                    if (monthData) {
                        const found = monthData.data.find(vb => vb.ten_loai_van_ban === typeName);
                        return found ? found.so_luong : 0;
                    }
                    return 0;
                });

                return {
                    label: typeName,
                    data,
                    backgroundColor,
                    borderColor,
                    borderWidth: 1,
                };
            });

            // Step 3: Render chart
            this.chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: true,
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            grid: {
                                display: true
                            },
                            stacked: true,
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        },
                    },
                }
            });
        },
    },
};
</script>

<style scoped>
canvas {
    max-height: 300px;
}

/* Gradient backgrounds for cards */
.card-gradient {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
    border-radius: 10px;
    overflow: hidden;
}

.card-gradient:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.card-blue {
    background: linear-gradient(135deg, #55cdff, #5f9cfd);
}

.card-green {
    background: linear-gradient(150deg, #8dfd68, #4ca728);
}

.card-purple {
    background: linear-gradient(150deg, #aa72fd, #5648bb);
}

.card-orange {
    background: linear-gradient(150deg, #feb92c, #f77f53);
}
/* Ensure text in cards is readable */
.card-blue h3,
.card-green h3,
.card-purple h3,
.card-orange h3 {
    color: #ffffff;
    font-weight: bold;
}

.card-blue p,
.card-green p,
.card-purple p,
.card-orange p{
    color: rgba(255, 255, 255, 0.8);
}

/* Style for router-link to remove default link styling */
.card-link {
    text-decoration: none;
    color: inherit;
    display: block;
}

/* Ensure cursor is pointer for clickable cards */
.cursor-pointer {
    cursor: pointer;
}
.card-orange.card-header a {
    color: #ffffff !important;
}
</style>

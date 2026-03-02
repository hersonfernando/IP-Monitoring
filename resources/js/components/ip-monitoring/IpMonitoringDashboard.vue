<template>
  <section class="ip-monitor">
    <h1>Office IP Monitoring</h1>

    <form class="ip-form" @submit.prevent="createIp">
      <input v-model="form.label" type="text" placeholder="Device Label (e.g. Core Router)" required />
      <input v-model="form.ip_address" type="text" placeholder="IP Address (e.g. 192.168.1.1)" required />
      <input v-model.number="form.port" type="number" min="1" max="65535" placeholder="Port" />
      <textarea v-model="form.notes" placeholder="Notes (optional)"></textarea>
      <button type="submit" :disabled="loading">Add IP</button>
    </form>

    <div class="toolbar">
      <button @click="fetchIps" :disabled="loading">Refresh</button>
    </div>

    <table>
      <thead>
        <tr>
          <th>Label</th>
          <th>IP</th>
          <th>Port</th>
          <th>Status</th>
          <th>Last Checked</th>
          <th>Response</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in ips" :key="item.id">
          <td>{{ item.label }}</td>
          <td>{{ item.ip_address }}</td>
          <td>{{ item.port }}</td>
          <td>
            <span :class="statusClass(item.last_status)">
              {{ item.last_status || "unknown" }}
            </span>
          </td>
          <td>{{ item.last_checked_at || "-" }}</td>
          <td>{{ item.last_response_ms ? `${item.last_response_ms} ms` : "-" }}</td>
          <td class="actions">
            <button @click="checkNow(item.id)" :disabled="loading">Check</button>
            <button @click="removeIp(item.id)" class="danger" :disabled="loading">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
  </section>
</template>

<script setup>
import { onMounted, reactive, ref } from "vue";
import { ipMonitoringApi } from "../../services/ipMonitoringApi";

const loading = ref(false);
const ips = ref([]);

const form = reactive({
  label: "",
  ip_address: "",
  port: 80,
  notes: "",
});

function resetForm() {
  form.label = "";
  form.ip_address = "";
  form.port = 80;
  form.notes = "";
}

function statusClass(status) {
  if (status === "online") return "status online";
  if (status === "offline") return "status offline";
  return "status unknown";
}

async function fetchIps() {
  loading.value = true;
  try {
    const { data } = await ipMonitoringApi.listIps({ per_page: 100 });
    ips.value = data.data ?? [];
  } finally {
    loading.value = false;
  }
}

async function createIp() {
  loading.value = true;
  try {
    await ipMonitoringApi.createIp(form);
    resetForm();
    await fetchIps();
  } finally {
    loading.value = false;
  }
}

async function checkNow(id) {
  loading.value = true;
  try {
    await ipMonitoringApi.checkNow(id);
    await fetchIps();
  } finally {
    loading.value = false;
  }
}

async function removeIp(id) {
  if (!window.confirm("Delete this monitored IP?")) return;
  loading.value = true;
  try {
    await ipMonitoringApi.deleteIp(id);
    await fetchIps();
  } finally {
    loading.value = false;
  }
}

onMounted(fetchIps);
</script>

<style scoped>
.ip-monitor {
  padding: 1rem;
  font-family: "Segoe UI", Tahoma, sans-serif;
}

.ip-form {
  display: grid;
  gap: 0.5rem;
  margin-bottom: 1rem;
  max-width: 40rem;
}

.toolbar {
  margin-bottom: 0.75rem;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th,
td {
  padding: 0.5rem;
  border-bottom: 1px solid #ddd;
  text-align: left;
}

.actions {
  display: flex;
  gap: 0.5rem;
}

.status {
  padding: 0.15rem 0.4rem;
  border-radius: 999px;
  text-transform: capitalize;
}

.status.online {
  background: #dbf4e2;
  color: #0e6b2c;
}

.status.offline {
  background: #ffd7d7;
  color: #8a1f1f;
}

.status.unknown {
  background: #ececec;
  color: #4f4f4f;
}

.danger {
  color: #fff;
  background: #b42318;
}
</style>

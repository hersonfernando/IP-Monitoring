import axios from "axios";

const baseUrl = "/api/ip-monitoring";

export const ipMonitoringApi = {
  listIps(params = {}) {
    return axios.get(`${baseUrl}/ips`, { params });
  },

  createIp(payload) {
    return axios.post(`${baseUrl}/ips`, payload);
  },

  updateIp(id, payload) {
    return axios.put(`${baseUrl}/ips/${id}`, payload);
  },

  deleteIp(id) {
    return axios.delete(`${baseUrl}/ips/${id}`);
  },

  checkNow(id) {
    return axios.post(`${baseUrl}/ips/${id}/check`);
  },

  listLogs(id, params = {}) {
    return axios.get(`${baseUrl}/ips/${id}/logs`, { params });
  },
};

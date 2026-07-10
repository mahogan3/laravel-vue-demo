import client from './client'

export const listCustomers = () => client.get('/customers').then((r) => r.data.data)
export const getCustomer = (id) => client.get(`/customers/${id}`).then((r) => r.data.data)
export const createCustomer = (payload) => client.post('/customers', payload).then((r) => r.data.data)
export const updateCustomer = (id, payload) => client.put(`/customers/${id}`, payload).then((r) => r.data.data)
export const deleteCustomer = (id) => client.delete(`/customers/${id}`)

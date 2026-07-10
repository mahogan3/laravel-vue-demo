import client from './client'

export const listOrders = () => client.get('/orders').then((r) => r.data.data)
export const getOrder = (id) => client.get(`/orders/${id}`).then((r) => r.data.data)
export const createOrder = (payload) => client.post('/orders', payload).then((r) => r.data.data)
export const updateOrderStatus = (id, status) =>
  client.patch(`/orders/${id}/status`, { status }).then((r) => r.data.data)
export const deleteOrder = (id) => client.delete(`/orders/${id}`)

export const environment = {
  production: true,
  apiUrl: (window as any).__env?.API_URL ?? 'https://api.vertex-admin.com/api',
  appName: 'Vertex-Admin'
};

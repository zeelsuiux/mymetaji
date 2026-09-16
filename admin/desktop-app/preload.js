const { contextBridge, ipcRenderer } = require('electron');

contextBridge.exposeInMainWorld('prishaSetup', {
  saveUrl: url => ipcRenderer.invoke('save-url', url),
  cancel: () => ipcRenderer.invoke('cancel-setup')
});

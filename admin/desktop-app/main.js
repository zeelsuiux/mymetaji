const { app, BrowserWindow, dialog, ipcMain } = require('electron');
const fs = require('fs');
const path = require('path');

const configFile = path.join(app.getPath('userData'), 'config.json');
const DEFAULT_URL = 'https://admin.mymetaji.com/login.php';
let setupWindow = null;

function getUrl() {
  try {
    const config = JSON.parse(fs.readFileSync(configFile, 'utf8'));
    return typeof config.url === 'string' ? config.url.trim() : '';
  } catch (_) {
    return '';
  }
}

function createSetupWindow() {
  return new Promise(resolve => {
    setupWindow = new BrowserWindow({
      width: 560,
      height: 360,
      resizable: false,
      autoHideMenuBar: true,
      webPreferences: {
        preload: path.join(__dirname, 'preload.js'),
        contextIsolation: true,
        nodeIntegration: false,
        sandbox: true
      }
    });
    setupWindow.on('closed', () => {
      setupWindow = null;
      resolve('');
    });
    setupWindow.loadURL('data:text/html;charset=utf-8,' + encodeURIComponent(`
      <!doctype html><html><head><meta charset="utf-8"><style>
      body{font-family:Segoe UI,sans-serif;padding:28px;color:#26352b;background:#f3f4f6}
      h2{margin:0 0 10px}p{color:#687568;font-size:14px}input{width:100%;box-sizing:border-box;padding:12px;border:1px solid #cbd5d1;border-radius:6px;font-size:15px;margin:10px 0 16px}button{padding:11px 18px;border:0;border-radius:6px;background:#02a9f7;color:white;font-size:14px;cursor:pointer;margin-right:8px}button.cancel{background:#64748b}
      </style></head><body><h2>Prisha ERP Desktop App</h2><p>Enter your Hostinger HTTPS website URL.</p><input id="url" placeholder="https://your-domain.com/cst_1/login.php" autofocus><div><button id="save">Open ERP</button><button class="cancel" id="cancel">Cancel</button></div><p id="error" style="color:#c0392b"></p><script>document.getElementById('save').onclick=async()=>{const value=document.getElementById('url').value.trim();if(!/^https:\/\//i.test(value)){document.getElementById('error').textContent='Please enter a valid HTTPS URL.';return;}await window.prishaSetup.saveUrl(value);};document.getElementById('cancel').onclick=()=>window.prishaSetup.cancel();</script></body></html>
    `));
    setupWindow.show();
  });
}

ipcMain.handle('save-url', async (_event, url) => {
  fs.mkdirSync(path.dirname(configFile), { recursive: true });
  fs.writeFileSync(configFile, JSON.stringify({ url }, null, 2));
  if (setupWindow) setupWindow.close();
  return true;
});
ipcMain.handle('cancel-setup', () => {
  if (setupWindow) setupWindow.close();
});

async function createMainWindow() {
  const url = DEFAULT_URL;
  if (!/^https:\/\//i.test(url)) {
    await dialog.showMessageBox({ type: 'error', title: 'HTTPS required', message: 'Please configure a Hostinger HTTPS URL.' });
    return app.quit();
  }
  const window = new BrowserWindow({
    width: 1440,
    height: 900,
    minWidth: 1024,
    minHeight: 700,
    autoHideMenuBar: true,
    backgroundColor: '#f3f4f6',
    webPreferences: { contextIsolation: true, nodeIntegration: false, sandbox: true }
  });
  try {
    await window.loadURL(url);
  } catch (error) {
    await dialog.showMessageBox({ type: 'error', title: 'Unable to open Prisha ERP', message: 'Check your Hostinger URL and internet connection.', detail: String(error.message || error) });
  }
}

app.whenReady().then(createMainWindow).catch(error => {
  dialog.showErrorBox('Prisha ERP failed to start', String(error.message || error));
  app.quit();
});
app.on('window-all-closed', () => { if (process.platform !== 'darwin') app.quit(); });

## 指令

### 啟動開發環境
`docker-composer up`

### 關閉開發環境
`docker-composer down`

### 初始化安裝依賴（安裝依賴後須重啟容器）
`docker-compose exec anser-saga-service composer install`
`docker-compose restart`
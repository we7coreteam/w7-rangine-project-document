const express = require('express')
const app = express()
const port = 3000

//mock数据转化
const makeMockData = (data) => {
	if(data.body&&data.body){
		var json=data.body;
		var Mock = require('mockjs')
		var mockData = Mock.mock(json)
		return mockData;
	}
	return data.body;
}

//POST中间件
const bodyParser = require('body-parser');
app.use(bodyParser.json());//数据JSON类型
app.use(bodyParser.urlencoded({ extended: false }));//解析post请求数据

//路由
app.all('/buildMock', (req, res, next) => res.json(makeMockData(req)))

//启动服务
app.listen(port, () => console.log(`app listening at http://localhost:${port}`))

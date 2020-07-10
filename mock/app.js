const express = require('express')
const app = express()
const port = 3000

const generateData = (data) => {
	console.log(data);
	// if(data.query&&data.query.record){
	// 	var json=data.query.record;
	// 	var obj = eval('(' + json + ')');
	// 	var Mock = require('mockjs')
	// 	var mockData = Mock.mock(obj)
	// 	return mockData;
	// }
	return {query:data.query,body:data.body};
}
var bodyp=require('body-parser');
//post中间件接收数据
app.use(bodyp.urlencoded({ extended: false,limit:20*1024})); //extended 拓展模式  limit 最大接收数据

app.all('/buildMock', (req, res, next) => res.json(generateData(req)))

app.listen(port, () => console.log(`app listening at http://localhost:${port}`))

const express = require('express')
const app = express()
const port = 3000

const generateData = (data) => {
	console.log(data);
	if(data.query&&data.query.record){
		var json=data.query.record;
		var obj = eval('(' + json + ')');
		var Mock = require('mockjs')
		var mockData = Mock.mock(obj)
		return mockData;
	}
	return [];
}

app.post('/buildMock', (req, res) => res.json(generateData(req)))

app.listen(port, () => console.log(`Example app listening at http://localhost:${port}`))

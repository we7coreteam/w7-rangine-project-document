const express = require('express')
const app = express()
const port = 9529

//mock数据转化
const makeMockData = (data) => {
	if(data.body&&data.body){
		var Mock = require('mockjs')
		var newData=Mock.mock(treeToTemplate(data.body,1));
		return romoveSlash(newData);
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


// num为1，对函数类型、正则执行函数，得到函数返回值
function treeToTemplate(tree, num = 0) {
	function parse(item, result) {
		let rule = item.rule ? ('|' + item.rule) : '';
		let value = item.default_value;
		if (item.default_value && item.default_value.indexOf('[') === 0 && item.default_value.substring(item.default_value.length - 1) === ']') {
			try {
				let reg = /\s*/g;
				let reg2 = /\"/g;
				value = value.substring(1, value.length - 1).replace(reg, '').replace(reg2, '').split(',');
				// console.error(123)
				// console.log(value)
				result[item.name + rule] = value
				// result[item.name + rule] = vm.run(`(${item.value})`)
			} catch (e) {
				result[item.name + rule] = value
			}
		} else {
			if (item.name.length) {
				switch (item.type) {
					case 1:
						// String
						result[item.name + rule] = value
						break
					case 2:
						// Number
						if (value === '') value = 1
						let parsed = parseFloat(value)
						if (!isNaN(parsed)) value = parsed
						result[item.name + rule] = value
						break
					case 3:
						// Boolean
						if (value === 'true') value = true
						if (value === 'false') value = false
						if (value === '0') value = false
						value = !!value
						result[item.name + rule] = value
						break
					case 4:
						// Object
						if (value) {
							// result[item.name + rule] = vm.run(`(${item.value})`)
							result[item.name + rule] = {};
							item.children.forEach((child) => {
								parse(child, result[item.name + rule])
							})
						} else {
							result[item.name + rule] = {}
							item.children.forEach((child) => {
								parse(child, result[item.name + rule])
							})
						}
						break
					case 5:
						// Array
						if (value) {
							try {
								// result[item.name + rule] = vm.run(`(${item.value})`)
								result[item.name + rule] = value
							} catch (e) {
								result[item.name + rule] = item.value
							}
						} else {
							result[item.name + rule] = item.children.length ? [{}] : []
							item.children.forEach((child) => {
								parse(child, result[item.name + rule][0])
							})
						}
						break
					case 6:
						// Function
						if (num == 1) {
							try {
								// 1
								let fun = eval(item.default_value);
								result[item.name + rule] = fun();

								// 2
								// let funcTest = new Function('return ' + item.default_value);
								// result[item.name + rule] = funcTest()()
							} catch (e) {
								// console.error(e);
								console.warn(`TreeToTemplate ${e.message}: ${item.type} { ${item.name}${rule}: ${item.default_value} }`) // 怎么消除异常值？
								result[item.name + rule] = item.default_value
							}
						} else {
							result[item.name + rule] = value
						}
						break
					case 7:
						// RegExp
						if (num == 1) {
							try {
								result[item.name + rule] = new RegExp(item.default_value);
							} catch (e) {
								console.warn(`TreeToTemplate ${e.message}: ${item.type} { ${item.name}${rule}: ${item.default_value} }`) // 怎么消除异常值？
								result[item.name + rule] = item.default_value
							}
						} else {
							let reg = /\\/g;
							result[item.name + rule] = item.default_value.replace(reg,"");
						}
						break
					case 8:
						// Null
						// tslint:disable-next-line: no-null-keyword
						result[item.name + rule] = null
						break
				}
			}
		}
	}
	let result = {}
	if (tree.length) {
		tree.forEach(child => {
			parse(child, result)
		})
	}
	return result
}

// 移除mock生成数据的'/'
function romoveSlash(obj) {
	let newObj = {}
	let reg = /\//g;
	for (let item in obj) {
		if (typeof obj[item] == 'string' && reg.test(obj[item])) {
			newObj[item] = obj[item].replace(reg,"");
		} else {
			newObj[item] = obj[item]
		}
	}
	return newObj
}



Apache Thrift
=====

The code provided by this library is from:
https://github.com/apache/thrift/tree/master/lib/php/lib

This repo has been created to provide the thrift protocol via composer


Thrift PHP Software Library

License
=======

Licensed to the Apache Software Foundation (ASF) under one
or more contributor license agreements. See the NOTICE file
distributed with this work for additional information
regarding copyright ownership. The ASF licenses this file
to you under the Apache License, Version 2.0 (the
"License"); you may not use this file except in compliance
with the License. You may obtain a copy of the License at

  http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing,
software distributed under the License is distributed on an
"AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
KIND, either express or implied. See the License for the
specific language governing permissions and limitations
under the License.

Dependencies
============

PHP_INT_SIZE

  This built-in signals whether your architecture is 32 or 64 bit and is
  used by the TBinaryProtocol to properly use pack() and unpack() to
  serialize data.

apc_fetch(), apc_store()

  APC cache is used by the TSocketPool class. If you do not have APC installed,
  Thrift will fill in null stub function definitions.

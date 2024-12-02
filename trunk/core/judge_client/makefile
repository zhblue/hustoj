all: judge_client.cc judge_client.http
	g++ -Wall -c -I/usr/local/mysql/include/mysql -I/usr/include/mysql -DOJ_USE_MYSQL judge_client.cc
	g++ -Wall -o judge_client judge_client.o -L/usr/local/mysql/lib/mysql -L/usr/lib/mysql  -L/usr/lib64/mysql -lmysqlclient
judge_client.http:judge_client.cc 
	g++ -o judge_client.http -O2 judge_client.cc
clean:
	rm judge_client judge_client.http

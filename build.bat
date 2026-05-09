cls
g++ main.cpp src/User.cpp src/Media.cpp src/Evaluation.cpp src/ServiceRequest.cpp src/Artisan.cpp src/Client.cpp -I . -I ".\mysql-connector-c-6.1.11-winx64\include" -L ".\mysql-connector-c-6.1.11-winx64\lib" -lmysql -o MonApp.exe
.\MonApp.exe

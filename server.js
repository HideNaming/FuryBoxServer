var http = require("http").Server()
var io = require("socket.io")(http, {
    cors: {
        origin: "http://localhost:3000",
        credentials: true
    }
})
var Redis = require("ioredis")

var redis = new Redis();
redis.psubscribe('box.*')
redis.psubscribe('stat-chanel')
redis.psubscribe('auction')

redis.on("pmessage", (pattern, channel, message) => {
    console.log(`Received ${message} from ${channel}`);
    message = JSON.parse(message)
    io.emit(channel+":"+message.event, message.data)
});

http.listen(6001)
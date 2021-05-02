var http = require("http").Server()
var io = require("socket.io")(http, {
    cors: {
        origin: "*",
        credentials: true
    }
})
var Redis = require("ioredis")

var redis = new Redis();
redis.psubscribe('box.*')
redis.psubscribe('stat-chanel')
redis.psubscribe('auction')

redis.on("pmessage", (pattern, channel, message) => {
    message = JSON.parse(message)
    io.emit(channel+":"+message.event, message.data)
});

http.listen(6001)
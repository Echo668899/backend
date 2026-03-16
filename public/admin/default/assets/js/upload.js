/**
 *
 */
class UploadChunk {
    constructor(options) {
        this.init(options);
        this.run();
    }

    init(options) {
        this.options = {
            url: options.url,
            files: options.files,
            project: options.project,
            retry_num: options.retry_num ? options.retry_num : 3,
            chunk_size: options.chunk_size ? options.chunk_size : 1024 * 1024 * 2,
            process: options.process,
            success: options.success,
            error: options.error,
            before: options.before,
            source: options.source,
        };
    }

    run() {
        for (var i = 0; i < this.options.files.length; i++) {
            this.preprocess(this.options.files[i])
        }
    }

    preprocess(file) {
        if (file.size <= 0) {
            return;
        }
        var chunk_total = Math.ceil(file.size / this.options.chunk_size);
        var file_md5 = hex_md5(file.name);//伪md5
        if (typeof this.options.before === 'function') {
            this.options.before(file);
        }
        this.upload(file_md5, 0, chunk_total, file, this.options.retry_num)
    }

    upload(file_md5, chunk_index, chunk_total, file, curr_retry_num) {
        if (chunk_index >= chunk_total) {
            return;
        }
        let _this = this;
        let start = chunk_index * this.options.chunk_size, end = Math.min(file.size, start + this.options.chunk_size);

        let chunkData = file.slice(start, end);
        if (this.options.source == 'lsj') {
            var url = this.options.url + '&page=' + (chunk_index + 1) + '&total_page=' + chunk_total + '&md5=' + file_md5 + '&more_quality=0&preview=0&user_id=default&file_name=' + file.name;
            $.ajax({
                url: url,
                type: "post",
                data: chunkData,
                processData: false,
                contentType: false,//formData才开启
                headers: {
                    "Content-Type": "application/octet-stream"
                },
                success: function (res) {
                    console.log(`lsj 返回: ${JSON.stringify(res)}`);
                    if (res.status === 'y') {
                        if (typeof _this.options.process === 'function') {
                            _this.options.process(chunk_index, chunk_total, 1);
                        }
                        if (res.data && res.data.id) {
                            if (typeof _this.options.success === 'function') {
                                _this.options.success(res.data.id);
                                return;
                            }
                        }
                        chunk_index++;
                        _this.upload(file_md5, chunk_index, chunk_total, file, _this.options.retry_num);
                    } else {
                        if (typeof _this.options.error === 'function') {
                            _this.options.error(res.error);
                        }
                    }
                },
                error: function (error) {
                    if (curr_retry_num > 0) {
                        console.log(`第 ${chunk_index + 1} 片上传失败，准备重试，剩余 ${curr_retry_num - 1} 次`);
                        _this.upload(file_md5, chunk_index, chunk_total, file, --curr_retry_num);
                    } else {
                        if (typeof _this.options.error === 'function') {
                            _this.options.error(error);
                        }
                    }
                }
            });
        } else {
            let url = this.options.url + '?num=' + (chunk_index + 1) + '&md5=' + file_md5 + "&total=" + (chunk_total + 1) + "&upload_token=media&server=media" + "&project=" + this.options.project + "&name=" + file.name;
            let formData = new FormData();
            formData.append('file', chunkData);
            $.ajax({
                url: url,
                // data: chunkData,
                data: formData,
                async: true,
                type: "post",
                processData: false,
                // contentType: "application/octet-stream",
                contentType: false,//formData才开启
                success: function (res) {
                    console.log(`普通返回: ${JSON.stringify(res)}`);
                    if (res.status === 'y') {
                        if (typeof _this.options.process === 'function') {
                            _this.options.process(chunk_index, chunk_total, 1);
                        }
                        if (res.data && res.data.uploadId) {
                            if (typeof _this.options.success === 'function') {
                                _this.options.success(res.data.uploadId);
                                return;
                            }
                        }
                        chunk_index++;
                        _this.upload(file_md5, chunk_index, chunk_total, file);
                    } else {
                        if (typeof _this.options.error === 'function') {
                            _this.options.error(res.error);
                        }
                    }
                }, error: function (error) {
                    if (curr_retry_num > 0) {
                        console.log(`第 ${chunk_index + 1} 片上传失败，准备重试，剩余 ${curr_retry_num - 1} 次`);
                        _this.upload(file_md5, chunk_index, chunk_total, file, --curr_retry_num);
                    } else {
                        if (typeof _this.options.error === 'function') {
                            _this.options.error(error);
                        }
                    }
                }
            })
        }

    }
}

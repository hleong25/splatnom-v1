Since we're using LESS (dynamic css), the sub folders in this directory
must be writable to everybody (777). That way when apache generates the
css, it can be saved in the directory. This root directory will not be
writeable for apache.


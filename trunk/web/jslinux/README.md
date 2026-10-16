# NOI Linux 2.0 桌面 — jslinux 真实 Linux 内核模块

本目录提供基于 [Fabrice Bellard JSLinux](https://bellard.org/jslinux/) 的去混淆版本（[levskaya/jslinux-deobfuscated](https://github.com/levskaya/jslinux-deobfuscated)），让用户在浏览器中启动一个完整的 x86 Linux 内核作为命令行环境。

## 目录内容

| 文件 | 用途 |
|------|------|
| `index.html` | 入口页面（自动加载 emulator） |
| `cpux86-ta.js` | x86 CPU 模拟器核心（447 KB） |
| `vmlinux-2.6.20.bin` | Linux 2.6.20 内核映像（1.7 MB） |
| `root.bin` | 根文件系统映像（2 MB） |
| `linuxstart.bin` / `linuxstart-20110820.tar.gz` | 启动引导数据 |
| `term.js` | 终端 I/O 模拟（VGA 文本模式） |
| `Serial.js` `PIT.js` `PIC.js` `CMOS.js` `KBD.js` | 各种硬件外设模拟 |
| `PCEmulator.js` | PC 整机 glue 代码 |
| `jslinux.js` | 主入口 / 二进制加载 |
| `clipboard.js` | 剪贴板桥接 |
| `LICENSE` | 原始 Bellard 授权声明（仅供学习） |
| `README.original.md` | 上游项目的原始 README |

## 在 NOI 桌面中使用

`noi.html` 的终端应用顶部有一个 **「🐧 切换到 jslinux」** 按钮，点击后会在新窗口里嵌入 `jslinux/index.html`。第一次加载需要下载约 4 MB 的 JavaScript + 内核 + 文件系统，之后完全在本地运行。

启动后会自动进入一个最小化 Linux 2.6.20 系统，包含：

- 完整 Linux 命令行（`ls` / `cat` / `cd` / `vi` / `gcc` / `make` 等）
- BusyBox 工具集
- 极小的 rootfs（适合作为学习用途，不能编译 C++ 标准库）

## 与 $OJ_TEST_RUN 的关系

| 模式 | 用途 |
|------|------|
| **仿真终端**（默认） | 通过 `g++ file.cpp` 调用 HUSTOJ `submit.php?ajax`（id=-1000），使用真实 OJ 后端判题 |
| **jslinux 真实 Linux** | 在浏览器中启动完整 Linux，但仅供命令行练习，不能直接编译 OI 程序（rootfs 不含完整工具链） |

## 部署到 HUSTOJ

把整个 `noi.html` + `jslinux/` 目录复制到 HUSTOJ web 根目录即可：

```bash
cp noi.html /home/judge/src/web/
cp -r jslinux/ /home/judge/src/web/
```

用户访问 `https://你的OJ域名/noi.html` 即可。

## 许可

JSLinux 源码遵循原作者 Fabrice Bellard 的协议（仅供学习和非商业用途）。
详细授权请见 `LICENSE` 及 `README.original.md`。

> **提示**：这个项目（levskaya/jslinux-deobfuscated）是 2014 年的旧版本，加载过程依赖同步 XHR（部分浏览器会拦截）。如果需要现代浏览器支持，建议改用 [copy/v86](https://github.com/copy/v86) 项目作为替代——只要把 `v86.wasm`、`libv86.js` 和一个 Linux 磁盘映像放到 web 目录中即可使用。

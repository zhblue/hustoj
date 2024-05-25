# LiveCD 解析

### LiveCD 的实现

通过 `uck` 工具解压出 `Ubuntu LiveCD` 的 `chroot` 环境，并在其中删除 `oo` 、 `gnome` 等大型程序释放空间，然后用 `apt` 工具安装基础环境，安装配置 `lxde` 和 `hustoj` 。再使用 `uck` 重新打包形成 `iso`。

### 升级方式

统一用[/home/judge/src/install/fixing.sh](http://dl.hustoj.com/fixing.sh) 脚本，既能修复也能升级。

# BilibiliBangumi
> 移植自 WordPress 主题
[Sakurairo](https://github.com/LovesAsuna/Sakurairo)
的追番插件

## 预览图


## 使用方法
1. 将正在使用的主题的默认模板复制一份
2. 如果模板主题没有以下代码请添加，如有，请跳过此步骤
```injectablephp
<?php
/**
 * 追番列表
 *
 * @package custom
 */
?>
```
3. 在输出文章的部分(我这里是 <?php $this->content(); ?>)的下面添加一条 <?php BiliBangumi_Plugin::output(); ?>
4. 接下来在后台→管理→独立页面中新增一个独立页面。 此时应该会在右侧【自定义模板】中找到【追番列表】四个字，使用它。 内容和标题自己适当写一下发布即可大功告成！
5. 访问下这个页面看看效果吧！

### 作者博客预览效果
[<预览点这里>](https://blog.hyosakura.com/index.php/bangumi.html)
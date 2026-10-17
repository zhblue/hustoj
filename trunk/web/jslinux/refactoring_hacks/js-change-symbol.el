;;
;; Simple hack to use JSShaper script reify.js to change symbolnames on the fly in an emacs buffer.
;; assumes existence of /var/tmp and unix tools
;;
(defun js-change-symbol (name1 name2)
  (interactive "sOld Symbol Name:
sNew Symbol Name: ")
  (let (commandstr (curline (current-line)) (reifypath "/path/to/reify.js"))
    (setq commandstr (concat "cat >/var/tmp/refactoring_tmp; node " reifypath " /var/tmp/refactoring_tmp " name1 " " name2))
    (message "Command: %s" commandstr)
    ;;this does most of the work:
    (shell-command-on-region 1 (point-max) commandstr t t )
    (goto-line curline)
    )
  )

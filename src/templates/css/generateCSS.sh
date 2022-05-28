#!/bin/bash
themes=("orange" "purple" "grey" "green")
for theme in ${themes[@]}; do
  echo "Generating css for theme: $theme"
  lessc less/$theme-theme.less $theme.css
done


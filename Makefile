FILES = $(shell find files -type f)
WCF_FILES = $(shell find files_wcf -type f)

all: be.bastelstu.josh.ps.tar be.bastelstu.josh.ps.tar.gz

be.bastelstu.josh.ps.tar.gz: be.bastelstu.josh.ps.tar
	gzip -9 < $< > $@

be.bastelstu.josh.ps.tar: files.tar acptemplates.tar *.xml LICENSE install.sql language/*.xml
	tar cvf $@ --mtime="@$(shell git log -1 --format=%ct)" --owner=0 --group=0 --numeric-owner --exclude-vcs -- $^

files.tar: $(FILES)
acptemplates.tar: acptemplates/*.tpl

%.tar:
	tar cvf $@ --mtime="@$(shell git log -1 --format=%ct)" --owner=0 --group=0 --numeric-owner --exclude-vcs -C $* -- $(^:$*/%=%)

clean:
	-rm -f files.tar
	-rm -f acptemplates.tar

distclean: clean
	-rm -f be.bastelstu.josh.ps.tar
	-rm -f be.bastelstu.josh.ps.tar.gz

constants.php: option.xml
	(echo "<?php" ; xq -r '.data.import.options.option[] | "define(\"" + (.["@name"] | ascii_upcase) + "\", " + .defaultvalue + ");"' < option.xml) > constants.php

.PHONY: distclean clean

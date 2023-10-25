<?php
// Lastedit extension, https://github.com/pftnhr/yellow-lastedit

class YellowLastedit {
    const VERSION = "0.8.18";
    public $yellow;         // access to API

    // Handle initialisation
    public function onLoad($yellow) {
        $this->yellow = $yellow;
        $this->yellow->language->setDefaults([
            "Language: de",
            "LasteditText: Zuletzt bearbeitet",
            "Language: en",
            "LasteditText: Last edited",
            "Language: sv",
            "LasteditText: Senast redigerad",
        ]);
    }

    // Handle page meta data
    public function onParseContentShortcut($page, $name, $text, $type) {
        $output = null;
        if ($name=="lastedit" && ($type=="block" || $type=="inline")) {
            list($lasteditText) = $this->yellow->toolbox->getTextArguments($text);
            if (is_string_empty($lasteditText)) $lasteditText = $this->yellow->language->getText("lasteditText");
            if ($page->get("published")) {
                $lasteditPub = strtotime($page->get("published"));
                $lasteditMod = $page->getLastModified($httpFormat = false);
                $lasteditDiff = $lasteditMod - $lasteditPub;
                
                if ( $lasteditDiff >= "86401" ) {
                    $output .= "<p class=\"lastedit\">" . $lasteditText . ": " . $page->getDateHtml("modified") . "</p>";
                }
            } else {
                $output = false;
            }
        }
        return $output;
    }
    
    // Handle page extra data
    public function onParsePageExtra($page, $name) {
        $output = null;
        if ($name=="lastedit") {
            $output = $this->onParseContentShortcut($page, "lastedit", "", "block");
        }
        if ($name=="header") {
            $extensionLocation = $this->yellow->system->get("coreServerBase").$this->yellow->system->get("coreExtensionLocation");
            $output = "<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"{$extensionLocation}lastedit.css\" />\n";
        }
        return $output;
    }
}

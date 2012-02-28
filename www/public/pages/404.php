<html>
<head>
    <title>splatom</title>
    <style>
        body{
            background: silver;
            text-align: center;
        }
        div.zoidberg{
            width: 50em;
            padding: 1em;
            margin: 0px auto;
            border: 0.3em solid black;
            background: white;
        }
        pre{
            font-family: 'Courier New',Courier,monospace;
            font-size: 0.5em;
        }
    </style>
</head>
<body>
    <br/><br/>
    <div class="splatnom">
        <a href="http://<?=$_SERVER['SERVER_NAME']?>">go back to splatnom</a>
    </div>
    <br/>
    <div class="zoidberg">
        <span>oops 404... why not zoidberg?</span>
        <?=zoidberg()?>
    </div>
</body>
</html>
<?php

function zoidberg()
{
    if (rand()%2)
        return zoidberg_small();
    else
        return zoidberg_big();
}

function zoidberg_small()
{
    return<<<EOA
<pre>
                                                                       xmHTTTTT%ms.
                                                                    z?!!!!!!!!!!!!!!?m
                                                                  z!!!!!!!!!!!!!!!!!!!!%
                                                               eHT!!!!!!!!!!!!!!!!!!!!!!!L
                                                              M!!!!!!!!!!!!!!!!!!!!!!!!!!!>
                                                           z!!!!!!!!!!XH!!!!!!!!!!!!!!!!!!X
                                                           "$\$F*tX!!W?!!!!!!!!!!!!!!!!!!!!!
                                                           >     M!!!   4$\$NX!!!!!!!!!!!!!t
                                                           tmem?!!!!?    ""   "X!!!!!!!!!!F
                                                      um@T!!!!!!!!!!!!s.      M!!!!!!!!!!F
                                                   .#!!!!!!!!!!!!!!!XX!!!!?mM!!!!!!!!!!t~
                                                  M!!!@!!!!X!!!!!!!!!!*U!!!!!!!!!!!!!!@
                                                 M!!t%!!!W?!!!XX!!!!!!!!!!!!!!!!!!!!X"
                                                :!!t?!!!@!!!!W?!!!!XWWUX!!!!!!!!!!!t
                                                4!!$!!!M!!!!8!!!!!@$$$$$\$NX!!!!!!!!-
                                                 *P*!!!$!!!!E!!!!9$$$$$$$$%!!!!!!!K
                                                    "H*"X!!X&!!!!R**$$$*#!!!!!!!!!>
                                                        'TT!?W!!9!!!!!!!!!!!!!!!!M
                                                        '!!!!!!!!!!!!!!!!!!!!!!!!F
                                                        '!!!!!!!!!!!!!!!!!!!!!!!!>
                                                        '!!!!!!!!!!!!!!!!!!!!!!!M
                                                        J!!!!!!!!!!!!!!!!!!!!!!!F K!%n.
         @!!!!!??m.                                  x?F'X!!!!!!!!!!!!!!!!!!!!HP X!!!!!!?m.
Z?L      '%!!!!!!!!!?s                            .@!\~ MB!!!!!!!!!!!!!!!!!U#!F X!!!!!!!!X#!%.
E!!N!k     't!!!!!!!!!?:                       zTX?!t~ M!t!!!!!!!!!!!!!!UM!!!F 4!!!!!!!!t%!!!!?.
!!!!!!hzh.   "X!!!!!!!!!>                  .+?!!3?!X  Z!!!B!!!!!!!!!!UM!!!!!" 4!!!!!!!!t?!!!!!!!h
?!!!!!!!!!*!?L %!!!!!!!!?               .+?!!!!3!!\  P!!!!?X!!!!!!U#!!!!!!X" 4!!!!!!!!\%!!!!!!!!!?
'X!!!!!!!!!!!!?TTTT*U!!!!k            z?!!!!!!t!!!- J!!!!!!9!!X@T!!!!!!!!X~ d!!!!!!!!!%!!!!!!!!!!!!L
 4!!!!!!!!!!!!!!!!!!!!!!!M          'W!!!!!!!X%!!P  %!!!!!!!T!!!!!!!!!!!X~ J!!!!!!!!!P!!!!!!!!!!!!!!\
  5!!!!!!!!!!!!!!!!!!!!!!!?m.       .@Ti!!!!!Z!!t  d!!!!!!!!!!!!!!!!!!!X-.JUUUUX!!!!J!!!!!!!!!!!!!!!!!
   %!!!!!!!!!!!!!!!!!!!!!!!!!!!TnzT!!!!!#/!!?!!X"  ^"=4UU!!!!!!!!!!U@T!!!!!!!!!!!!Th2!!!!!!!!!!!!!!!!!!
    ^t!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!?K!K!!f               `""#X!!!!!!!!!!!!!!!!!?t!!!!!!!!!!!!!!!!(>
       "U!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!$!!F                      "tX!!!!!!!!!!!!!!!!b!!!!!!!!!!!!!!!(>
          '"*tUUX!X!!!!!!!!!!!!!!!!!!!!!!!!$!Z                          ^4!!!!!!!!!!!!!!!N!!!!!!!!!!!!!!!!
                 %!!!!!!!!!!!!!!!!!!!!!!!!X!X                              %W@@WX!!!!!!!!!N!!!!!!!!!!!!!!!
                  "X!!!!!!!!!!!!!!!!!!!!!@!!*        ..    ..  :m.. ETThmuM!!!!!!!!!!!!!!!!@m@*TTTT?!!!W!!
                    %!!!!!!!!!!!!!!!!!!W?!!X         M!!!TT?!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!9UU!!!!!!!!!M!f
                     't!!!!!!!!!!!!!!!P!!!!X          5!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!?NX!!!!!!L
                       "W!!!!!!!!!!!X#!!!!!R           "X!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!R!!!!!t
                         ^*X!!!!!!!t%!!!!!h              %X!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!>
                             "*U!!M!!!!!!X~ :?!!!T!+s...   *X!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                                 :?!!!!!!> :?!!!!!!!!!!!!!!!!?tX!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!>
                                 %!!!!!!F .%!!!!!!!!!!!!!!!!!!!!!#4U!!!!!!!!!!!!U!!!!!!!!!!!!!!!!!!!!!!!!~
                                K!!!!!!Z  K!!!!!!!!!!!!!!!!!!!!!!!  F!!!!!?!!?X!!!!!!!!!!!!!!!!!!!!!!!!!Z
                               X!!!!!!t  H!!!!!!!!!!!!!!!!!!!!!!!!> !!!!!!!!!!!W!!!!!!!!!!!!!!!!!!!!!!!t
                               %!!!!!!F :!!!!!!!!!!!!!!!!!!!!!!!!!> !!!!!!!!!!!!#X!!!!!!!!!!!!!!!!!!!!X
                              '!!!!!!X  K!!!!!!!!!!!!!!!!!!!!!!!!!> K!!!!!!!!!!!!!?W!!!!!!!!!!!!!!!!X"
</pre>
<a href="http://www.gotfuturama.com/Multimedia/AsciiArt/ZoidbergDopyLook.shtml">source</a>
EOA;
}

function zoidberg_big()
{
    return<<<EOA
<pre>
                                                                            ^OMMMMMMMMMM!.
                                                                      IMM6|!|!|!|!|!|!|!|!|QMI
                                                                  QM6||!|!|!|!|!|!|!|!|!|!|!|!OMQ
                                                               MM||!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|MQ
                                                           6M|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!QQ
                                                         QM|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|6M
                                                   OMMQQ|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|Q|
                                                 !MMMMMMQ|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!IM`
                                              MO` `^      OM|!|!|!|!|!|6QMMQOI!|!|!|!|!|!|!|!|!|!|!|!|M`
                                            .M   .MM!       Q6|!|!|MI`         |MO|!|!|!|!|!|!|!|!|!|O6
                                            M                MO!|QQ     MMM`      !M|!|!|!|!|!|!|!|!|M
                                            M                MM!6Q                  M|!|!|!|!|!|!|!|M!
                                            QI               M6!OO                  QI|!|!|!|!|!|!|OO
                                             `MI         `6QM!|!|M^                .M!|!|!|!|!|!|!M|
                                             OOMMMMM6I|!|!|!|!|!|!QM              !M|!|!|!|!|!|!|MI
                                           6M6!|!|!|!|!|!|!|!|!|!|!|QM6         QMI!|!|!|!|!|!|IM^
                                        |MQ!|!|!|!|!|!|!|!|!|!|!|!|!|!||QMMMMMM|!|!|!|!|!|!|!|IM!
                                       MI|!|!MI|!|!|!|!|!|!|!|!|!|!|!|MM6|!|!|!|!|!|!|!|!|!|!IM
                                     !M|!|!|MO!|!|!M|!|!|!|!|!|!|!|!|!|!|!I6QMMM||!|!|!|!|!|IM
                                    IQ!|!|!M||!|!|QO!|!|!OQ|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|M
                                   MQ|!|!IM|!|!|!IM|!|!|!6Q|!|!|!|MMMMMM!|!|!|!|!|!|!|!|!|!M
                                MQ|!|!|6M|!|!|!|IM!|!|!||M!|!|!||MMMMMMMMM|!|!|!|!|!|!|!|!M
                               Q6|!|!|OQ!|!|!|!MM|!|!|!|M|!|!|!|MMMMMMMMMMMM!|!|!|!|!|!|!OO
                                M6|!|OM!|!|!|!MO|!|!|!MM|!|!|!|MMMMMMMMMMMMM|!|!|!|!|!|!IM
                                  `^^ M|!|!|!MQ|!|!|!M6|!|!|!|OMMMMMMMMMMMMM6!|!|!|!|!|!OM
                                       !MMMM^MI|!|!|IM!|!|!|!MMMQQMMMMMMMMMM|!|!|!|!|!|!6M
                                              QMMMQMMM|!|!||M!^^^^^^^^^^MMO!|!|!|!|!|!|!|Q|
                                              `MI!|!|!MMMMMOMQI^^^^^^^6MO!|!|!|!|!|!|!|!|!M
                                              ^M|!|!|!|!|!|!|!|!|II|!|!|!|!|!|!|!|!|!|!OMMQOOMM!
                                           .MMQO|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|MMOOOOOOOOOOM|
                                        !MQOOOM|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|IMOOOOOOOOOOOOOOOQM.
                                      .MOOOOOOM!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!6MOOOOOOOOOOOOOOOOOOQMMM
                                     MQOOQMMMMQ|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!MMQQOQOQOOOOOOOOOOOMO|!|!6M
                                    MMMI^`^`^MI|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!6M`^`^`^`^`^`^`|QMQM6|!|!|!|!|M^
                                 `MM^^`^`^`^`M|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!M|^`^`^`^`^`^`^`^`MI|!|!|!|!|!|!|MM
                                MMM^^`^`^`^`^M|!|!|!|!|!|!|!|!|!|!|!|!|!|!MM`^`^`^`^`^`^`^`^!M|!|!|!|!|!|!|!|!|OM
                              .M|M^`^`^`^`^`^M!|!|!|!|!|!|!|!|!|!|!|!|!|6M`^`^`^`^`^`^`^`^`^M!|!|!|!|!|!|!|!|!|!|OM
                             M6!M^`^`^`^`^`^`MI|!|!|!|!|!|!|!|!|!|!|!|!MI^`^`^`^`^`^`^`^`^`M|!|!|!|!|!|!|!|!|!|!|!|QM
                          ^M6!|Q6^`^`^`^`^`^!!M!|!|!|!|!|!|!|!|!|!|!6M6^`^`^`^`^`^`^`^`^`^M||!|!|!|!|!|!|!|!|!|!|!|!||M`
                         QO!|!|M`6MMMQOOOOOOOOOMI!|!|!|!|!|!|!|!|!IMOOOOOOOOOOOOQMQMMMQ^^|M|!|!|!|!|!|!|!|!|!|!|!|!|!|!6M
                        M||!|!OQOOOOOOOOOOOOOOOOMM|!|!|!|!|!|!|!6MOOOOOOOOOOOOOOOOOOOOOOQM||!|!|!|!|!|!|!|!|!|!|!|!|!|!|!M.
                       M||!|!|MOOOOOOOOOOOOOOOOOOOMMI!|!|!|!|QMMOOOOOOOOOOOOOOOOOOOOOOOOQM|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!MM
                      MI|!|!|6MOOOOOOOOOOOOOOOOOOOOOOQMMMMQQOOOOOOOOOOOOOOOOOOOOOOOOOOOOQM!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|M.
                     MI!|!|!|MOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOMM!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!M`
                    MQ|!|!|!|MOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOM|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!M`
                   M||!|!|!|!MOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOM|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!M.
                  IQ|!|!|!|!|MOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOM6|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!M
                 ^M|!|!|!|!OMOOOOQMMMMMMQOI^^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^^|I6QMMMMMMQMM!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!IM`
                 M|!|!|!|!MQ6|^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^MI|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!MM
                Q6|!|!|!|M^^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`OMMMQ|!|!|!|!|!|!|!|!|!|!|!|!|!|!||Q!
               .M!|!|!|IM^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`OQ!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|M
               MI|!|!|6M^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`6M!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!M.
              ^M|!|!|QQ`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^!M!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|OI
             ^M!|!|!MO^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^M||!|!|!|!|!|!|!|!|!|!|!|!|!|!IM
             MM|!|!M`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^MM|!|!|!|!|!|!|!|!|!|!|!|!|!||M
            `M|!|!M!^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^^M|!|!|!|!|!|!|!|!|!|!|!|!|!|M
            M!|!|M^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^MI!|!|!|!|!|!|!|!|!|!|!|!|!M`
            M|!|Q6^`^`^`^`^`^^|IOQMMMMMMMMMMMMMMMQMQQQQQQQQQQMMMMMMMMMMMMMMMMMO6|!!^^`^`^`^`^`^`^`^`^`^`^`M|!|!|!|!|!|!|!|!|!|!|!|!|66
           IO!||MMMMMMOQOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOQOQMMMMMMMMQ6I|^!^M|!|!|!|!|!|!|!|!|!|!|!|!|M
           M|!|MOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOQQ!|!|!|!|!|!|!|!|!|!|!|!|M.
           M!|!MOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOM|!|!|!|!|!|!|!|!|!|!|!|!|M
          `M|!|MOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOMI|!|!|!|!|!|!|!|!|!|!|!|!M`
          IO|!IMOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOQ6|!|!|!|!|!|!|!|!|!|!|!|!6M
          I6|!6MOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOMQ|!|!|!|!|!|!|!|!|!|!|!|!|!|!QMI
          QI|!|MOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOQM6|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!M6
          I6|!|MOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOM|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!IM
          `Q|!|MOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOM!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!||M
           M!|!|MOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOM||!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|Q6
           |Q!|!MOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOM|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|MO
            MI!||M6MMMMMMQOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOM6!|!|!|!|!|!|!|MMM6|!|!|!|!|!|!|!|!|!|!|!|!|QM
             MM!|MI^`^`^`^`^`^!IQMMMMMMMQQOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOQQMMMMMMQ6|^`^`^M||!|!|!|!|!|MQ`^`!M|!|!|!|!|!|!|!|!|!|!|!|!|6M
               M!|M^`^`^`^`^`^`^`^`^`^`^`^`^`^`^!!I66OOQQQMMMMMMMQQOOO6I|!!^^`^`^`^`^`^`^`^`^`^`^`QI|!|!|!|!|MM^`^`6M|!|!|!|!|!|!|!|!|!|!|!|!|!IM.
                QOO6^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`6O|!|!|!|!M^^`^`^MO|!|!|!|!|!|!|!|!|!|!|!|!|!6M
                  QM`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`M|!|!|!IM^`^`^`^^QM6|!|!|!|!|!|!|!|!|!|!|!|!MM
                   IQ`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^M6|!|!6M`^`^`^`^!M^M||!|!|!|!|!|!|!|!|!|!|!|M`
                    MO`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^M||!6M`^`^`^`^`6M MI|!|!|!|!|!|!|!|!|!|!|!M^
                    QM^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^MMM6`^`^`^`^`^MQ QMMIM||!|!|!|!|!|!|!|!|IQ
                    .MI!`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^|M.    ^Q!|!|!|!|!|!|!|!|!|M
                     |MOOQMMMI^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`M.     O6|!|!|!|!|!|!|!|!|M
                     .MOOOOOOOOOOOOOQQMMMMMM6|^^`^`^`^`^`IMMO^^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^IQ      ^M6MO|!|!|!|!|!|!|M
                      MOOOOOOOOOOOOOOOOOOOOOOOOOOOOQQQMMMMMMMMMOMMM!`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`I6MMM           MI|!|!|!|!|!6M
                      MOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOM    IMMQQQQQO|I||!!^`^`^`^`^`^`^`^|6QMMMMOOOOOOM!           MI!|!|!|!|MI
                      QOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOM     MOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOM             O MI!|!IM
                      OQOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOQM     MOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOM|               |M||M!
                      MQOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOM|     MOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOM!                 .
                       QMMMQQOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOMM      MOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOQ
                         QI!|!|||66OMMMMMMMMMMMMMMMMMMMMMMMM!          MQOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOQ
                         6O|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!||M              IMOMMMMMMQQOOOOOOOOOOOOOOOOOOOOOQMMMM
                         ^Q!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!M|               O6|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|M!
                         `M!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!M                .M|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|M
                          M!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|O6                 M|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!Q6
                          M|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!M                  MI|!|!|!|!|!|!|!|!|!|!|!|!|!|!|M`
                          M!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|M                  IO|!|!|!|!|!|!|!|!|!|!|!|!|!|!|M^
                         ^M!|!|!|!|!|!|!|!|!|!|!|!|!|!|!6Q                  |O|!|!|!|!|!|!|!|!|!|!|!|!|!|!|M!
                         6Q!|!|!|!|!|!|!|!|!|!|!|!|!|!|!QI                  IO|!|!|!|!|!|!|!|!|!|!|!|!|!|!|QI
                         MI!|!|!|!|!|!|!|!|!|!|!|!|!|!|!M|                  Q6|!|!|!|!|!|!|!|!|!|!|!|!|!|!|IQ
                        .M!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|Q|                  M|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|M
                      !M6!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!IM                  M|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|M
                QMMI!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!M                ^M||!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|O|
          |MQMM|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!OI               MQ|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|M`
         OMM6`6MIOMMMMMQ6||!|!|!|!|!|!|!|!|!|!|!|!|!|!|!||M              MI!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|MO
        M|!|MMQ!^`^`^`^`^`^6QMM||!|!|!|!|!|!|!|!|!|!|!|!|!M             MI|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|MM
      ^M|!MMQOOQMMMMO|`^`^`^`^`^6M6|!|!|!|!|!|!|!|!|!|!|!|Q|           M|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|!|QM
      MOIM!|!|!|!|!|!|!6MM^`^`^`^`^|MI!|!|!|!|!|!|IOMMMQ|^IQ         6M!!!|!|!|!|!|!|!|!|!|!|!|6QMMMMMMMMMM||6M
      M6M!|!|!|!|!|!|!|!|!|OMM^`^`^`^|M!!!6QMMQI!`^`^`^`^`IM      MM|^^`^`^^|OMMM6|!|!|IMMMM|^^`^`^`^`^`^`^`MMM
      IM^`^`|IIOQMMMMMMQO666I66M6QMMMMQ|^`^`^`^`^`^`!IMMM^      !M!^`^!OMMQI^`^`^`^!M!`^`^`^`^`^`^`^`^`^`^`^`|M.
        QM!^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`^`!6QMMMQ^             ^MMI!|!|!|!|!QM|`^`M!^`^`^`^!OMMMMOOOQQMMMMM6!!M
             `|QMMMMMMMMMMMMMMMMQO|!.                            M!M!|!|!|!|!|!|IMMM!6MMQ6!|!|!|!|!|!|!|!|OM6!OQ
                                                                 !M^MM|!|!|!|!|!|!QO|!|!|!|!|!|!|!|!|QMMQ!!^IM6
                                                                  6O`^OM|!|!|!|!|!O6|!|!|!|!|!||6MMI`^`^`^QM
                                                                   `MO`^`QMM|!|!|!|M!|!|!|6MMQ!^`^`^`^6MM.
                                                                      QM^`^`^`!I6OOO66!^^`^`^`^`^|QMO
                                                                         !MMM6!^`^`^`^`^!|OMMMI.


</pre>
<a href="http://www.futurama-madhouse.net/asciiart/ylb/zoidbathing.txt">source</a>
EOA;
}
?>

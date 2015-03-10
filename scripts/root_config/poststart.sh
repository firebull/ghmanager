#!/bin/bash
iptables -D INPUT -m conntrack --ctstate ESTABLISHED -j ACCEPT

iptables -D INPUT -p icmp -m conntrack --ctstate ESTABLISHED -j ACCEPT
iptables -D INPUT -s 188.64.168.3/32 -p udp -j ACCEPT
iptables -D INPUT -s 188.64.168.6/32 -p udp -j ACCEPT
iptables -D INPUT -p tcp -m conntrack --ctstate ESTABLISHED -j ACCEPT
iptables -D INPUT -p icmp -m conntrack --ctstate RELATED -j ACCEPT


iptables -I INPUT -p icmp -m conntrack --ctstate ESTABLISHED -j ACCEPT
iptables -I INPUT -s 188.64.168.3/32 -p udp -j ACCEPT
iptables -I INPUT -s 188.64.168.6/32 -p udp -j ACCEPT
iptables -I INPUT -s 212.24.32.128/27 -p udp -j ACCEPT # C01
#####################################################
# Веб-хостинг
iptables -I INPUT -s 188.64.172.50/32 -p udp -j ACCEPT
iptables -I INPUT -s 188.64.172.51/32 -p udp -j ACCEPT
iptables -I INPUT -s 188.64.172.52/32 -p udp -j ACCEPT
iptables -I INPUT -s 188.64.172.53/32 -p udp -j ACCEPT
iptables -I INPUT -s 188.64.172.54/32 -p udp -j ACCEPT
iptables -I INPUT -s 188.64.172.50/32 -p tcp -j ACCEPT
iptables -I INPUT -s 188.64.172.51/32 -p tcp -j ACCEPT
iptables -I INPUT -s 188.64.172.52/32 -p tcp -j ACCEPT
iptables -I INPUT -s 188.64.172.53/32 -p tcp -j ACCEPT
iptables -I INPUT -s 188.64.172.54/32 -p tcp -j ACCEPT


#####################################################
# Setti
iptables -I INPUT -s 176.9.50.13/32 -p udp -j ACCEPT
iptables -I INPUT -s 188.40.40.201/32 -p udp -j ACCEPT
iptables -I INPUT -s 46.4.71.67/32 -p udp -j ACCEPT
iptables -I INPUT -s 176.9.50.16/32 -p udp -j ACCEPT
#####################################################
# Block Tumen
#iptables -A INPUT -s 188.186.18.0/23 -j DROP
#iptables -A INPUT -s 188.186.20.0/22 -j DROP
#iptables -A INPUT -s 188.186.0.0/21 -j DROP
#iptables -A INPUT -s 188.186.8.0/21 -j DROP

# Block xakkerenok
iptables -I INPUT -s 213.88.113.16/32 -j DROP

# Block deadland attacker
iptables -A INPUT -s 74.208.15.17/32 -j DROP
#####################################################
iptables -I INPUT -p tcp -m conntrack --ctstate ESTABLISHED -j ACCEPT
iptables -I INPUT -p icmp -m conntrack --ctstate RELATED -j ACCEPT

iptables -I INPUT -p tcp -m tcp --dport 27015:27100 --tcp-flags FIN,SYN,RST,ACK SYN -m connlimit --connlimit-above 10 --connlimit-mask 32 -j DROP
# Временное правило для внешнего сервера под EAC
iptables -I INPUT -s 195.211.103.147/32 -d 188.64.172.155/32 -p udp -j ACCEPT
iptables -I INPUT -s 212.158.167.100/32 -d 188.64.172.155/32 -p udp -j ACCEPT
iptables -I INPUT -s 80.77.175.99/32 -d 188.64.172.155/32 -p udp -j ACCEPT
iptables -I INPUT -s 77.37.144.20/32 -d 188.64.172.155/32 -p udp -j ACCEPT
iptables -I INPUT -s 46.188.16.128/32 -d 188.64.172.155/32 -p udp -j ACCEPT
iptables -I INPUT -s 91.218.228.233/32 -d 188.64.172.155/32 -p udp -j ACCEPT
iptables -I INPUT -s 46.38.48.142/32 -d 188.64.172.155/32 -p udp -j ACCEPT
iptables -I INPUT -s 91.191.158.75/32 -d 188.64.172.155/32 -p udp -j ACCEPT

iptables -I INPUT -p udp -m string --string "Desudesudesu~" --algo bm  -j DROP
#iptables -I INPUT -p udp -m string --string "Desudesudesu~" --algo bm  -m limit --limit 1/m -j LOG --log-prefix 'UDP-FLOOD: ' --log-level info
iptables -I INPUT -p tcp -m string --string "Desudesudesu~" --algo bm  -j DROP
iptables -I INPUT -p tcp -m string --string "Desudesudesu~" --algo bm  -m limit --limit 1/m -j LOG --log-prefix 'TCP-FLOOD: ' --log-level info
iptables -I INPUT -p udp -m string --string "ody6SAMPBE" --algo bm  -j DROP
iptables -I INPUT -p udp -m string --string "ody6SAMPBE" --algo bm  -m limit --limit 1/m -j LOG --log-prefix 'SAMP-FLOOD: ' --log-level info

# New BASTARDS
iptables -I INPUT -p udp -m udp --dport 25000:31000 -m length --length 1400: -j REJECT_SRCDS_UDP_FLOOD
#####################################################
iptables -I INPUT -p udp -m udp --dport 64000:65535 -m length --length 1400: -j REJECT_SRCDS_UDP_FLOOD

# Мастер-серверы Valve и VAC  ##############################
iptables -I INPUT -p tcp -s 212.187.192.0/24 -j ACCEPT
iptables -I INPUT -p tcp -s 209.197.18.0/24 -j ACCEPT
iptables -I INPUT -p tcp -s 208.111.128.0/18 -j ACCEPT
iptables -I INPUT -p tcp -s 208.111.133.80/29 -j ACCEPT
iptables -I INPUT -p tcp -s 208.111.158.48/29 -j ACCEPT
iptables -I INPUT -p tcp -s 208.64.200.0/24 -j ACCEPT
iptables -I INPUT -p tcp -s 205.196.6.0/24 -j ACCEPT
iptables -I INPUT -p tcp -s 204.63.214.0/23 -j ACCEPT
iptables -I INPUT -p tcp -s 203.77.184.0/21 -j ACCEPT
iptables -I INPUT -p tcp -s 209.3.157.112/29 -j ACCEPT
iptables -I INPUT -p tcp -s 146.66.152.0/23 -j ACCEPT
iptables -I INPUT -p tcp -s 69.28.128.0/18 -j ACCEPT
iptables -I INPUT -p tcp -s 69.28.145.168/29 -j ACCEPT
iptables -I INPUT -p tcp -s 69.28.151.176/28 -j ACCEPT
iptables -I INPUT -p tcp -s 69.28.153.0/24 -j ACCEPT
iptables -I INPUT -p tcp -s 69.28.151.0/24 -j ACCEPT
iptables -I INPUT -p tcp -s 68.142.64.0/24 -j ACCEPT
iptables -I INPUT -p tcp -s 68.142.91.32/28 -j ACCEPT
iptables -I INPUT -p tcp -s 68.142.116.176/28 -j ACCEPT
iptables -I INPUT -p tcp -s 81.171.115.0/27 -j ACCEPT
iptables -I INPUT -p tcp -s 85.214.223.0/24 -j ACCEPT
iptables -I INPUT -p tcp -s 87.248.194.0/23 -j ACCEPT
iptables -I INPUT -p tcp -s 87.248.196.0/22 -j ACCEPT
iptables -I INPUT -p tcp -s 87.248.200.0/21 -j ACCEPT
iptables -I INPUT -p tcp -s 87.248.208.0/20 -j ACCEPT
iptables -I INPUT -p tcp -s 72.165.61.128/26 -j ACCEPT
iptables -I INPUT -p tcp -s 79.141.174.0/24 -j ACCEPT

iptables -I INPUT -p udp -s 212.187.192.0/24 -j ACCEPT
iptables -I INPUT -p udp -s 209.197.18.0/24 -j ACCEPT
iptables -I INPUT -p udp -s 208.111.128.0/18 -j ACCEPT
iptables -I INPUT -p udp -s 208.111.133.80/29 -j ACCEPT
iptables -I INPUT -p udp -s 208.111.158.48/29 -j ACCEPT
iptables -I INPUT -p udp -s 208.64.200.0/24 -j ACCEPT
iptables -I INPUT -p udp -s 205.196.6.0/24  -j ACCEPT
iptables -I INPUT -p udp -s 204.63.214.0/23 -j ACCEPT
iptables -I INPUT -p udp -s 203.77.184.0/21 -j ACCEPT
iptables -I INPUT -p udp -s 209.3.157.112/29 -j ACCEPT
iptables -I INPUT -p udp -s 146.66.152.0/23 -j ACCEPT
iptables -I INPUT -p udp -s 69.28.128.0/18 -j ACCEPT
iptables -I INPUT -p udp -s 69.28.145.168/29 -j ACCEPT
iptables -I INPUT -p udp -s 69.28.151.176/28 -j ACCEPT
iptables -I INPUT -p udp -s 69.28.153.0/24 -j ACCEPT
iptables -I INPUT -p udp -s 69.28.151.0/24 -j ACCEPT
iptables -I INPUT -p udp -s 68.142.116.176/28 -j ACCEPT
iptables -I INPUT -p udp -s 68.142.91.32/28 -j ACCEPT
iptables -I INPUT -p udp -s 68.142.64.0/24 -j ACCEPT
iptables -I INPUT -p udp -s 81.171.115.0/27 -j ACCEPT
iptables -I INPUT -p udp -s 85.214.223.0/24 -j ACCEPT
iptables -I INPUT -p udp -s 87.248.194.0/23 -j ACCEPT
iptables -I INPUT -p udp -s 87.248.196.0/22 -j ACCEPT
iptables -I INPUT -p udp -s 87.248.200.0/21 -j ACCEPT
iptables -I INPUT -p udp -s 87.248.208.0/20 -j ACCEPT
iptables -I INPUT -p udp -s 72.165.61.128/26 -j ACCEPT
iptables -I INPUT -p udp -s 79.141.174.0/24 -j ACCEPT

# media.steampowered.com
iptables -I INPUT -p tcp -s 87.248.207.253/32 -j ACCEPT
iptables -I INPUT -p tcp -s 87.248.207.254/32 -j ACCEPT

iptables -D reject_func -p tcp -j REJECT --reject-with tcp-reset
iptables -D reject_func -p udp -j REJECT --reject-with icmp-port-unreachable
iptables -D reject_func -j REJECT --reject-with icmp-proto-unreachable

iptables -A reject_func -p tcp -j DROP
iptables -A reject_func -p udp -j DROP
iptables -A reject_func -j DROP

iptables -I INPUT -s 178.132.203.148/32 -j ACCEPT
iptables -I OUTPUT -d 178.132.203.148/32 -j ACCEPT

###блокировка лишнего апача
iptables -N syn_flood
iptables -I syn_flood -j DROP
iptables -I syn_flood -m limit --limit 1/s --limit-burst 3 -j RETURN
iptables -I INPUT -p tcp --dport 443 --syn -j syn_flood
iptables -I INPUT -p tcp --dport 80 --syn -j syn_flood


#iptables -D input_ext -p tcp -m limit --limit 3/min -m tcp --dport 80 --tcp-flags FIN,SYN,RST,ACK SYN -j LOG --log-prefix "SFW2-INext-ACC-TCP " --log-tcp-options --log-ip-options
#iptables -D input_ext -p tcp -m tcp --dport 80 -j ACCEPT

#iptables -I INPUT -p tcp -m tcp -s 188.64.172.154/31 --dport 80 -j ACCEPT
#iptables -I INPUT -p tcp -m tcp -s 188.64.172.156/30 --dport 80 -j ACCEPT
#iptables -I INPUT -p tcp -m tcp -s 188.64.172.160/30 --dport 80 -j ACCEPT
#iptables -I INPUT -p tcp -m tcp -s 188.64.172.164/32 --dport 80 -j ACCEPT
#iptables -I INPUT -p tcp -m tcp -s 188.64.172.204/30 --dport 80 -j ACCEPT
#iptables -I INPUT -p tcp -m tcp -s 188.64.172.208/28 --dport 80 -j ACCEPT
#iptables -I INPUT -p tcp -m tcp -s 188.64.172.224/31 --dport 80 -j ACCEPT
#iptables -I INPUT -p tcp -m tcp -s 188.64.172.226/32 --dport 80 -j ACCEPT


exit 0

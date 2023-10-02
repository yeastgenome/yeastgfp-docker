postscript("temp.eps");

temp <- read.table("newout", header=TRUE)
DB <- temp[,1]
messageCount <- temp[,2]
proteinCount <- temp[,3]
mpp <- messageCount / proteinCount
logmpp <- log(mpp)
Xs <- array(99,c(length(temp[,1]),64))
sink("outmap");
mynames <- names(temp)
for (i in 4:67) {
  t2<-i-3
print(mynames[i])
print(t2)
  Xs[,t2]<- temp[mynames[i]][,1]
}


sink("outMessageVsProtein")
mymodel <- lm (proteinCount~messageCount)
summary(mymodel)
postscript("messageVsProtein.eps");
plot(messageCount,proteinCount)
dev.off()
postscript("logMessageVsLogProtein.eps");
plot(log(messageCount),log(proteinCount))
dev.off()

sink("outCodonUsage")
mymodel <- lm (mpp~Xs)
summary(mymodel)

postscript("codonUsageTCAVsLogMPP.eps")
plot(Xs[,29], logmpp)
title("log(message/protein) vs. TCA usage (serene)")
dev.off()

postscript("codonUsageTCAVsMPP.eps")
plot(Xs[,29], mpp)
title("(message/protein) vs. TCA usage (serene)")
dev.off()

#postscript("codonUsageTGGVsLogMPP.eps")
#plot(Xs[,27], logmpp)
#title("log(message/protein) vs. TCA usage (only tryptophan)")
#dev.off()





#predict(mymodel, new1, interval="confidence")
#plot(DB,messageCount)
#linearModel <- lm(messageCount~DB) 
#summary(linearModel)

#Xs <- array(45.3,c(6214,61))
#lm (mpp~Xs)






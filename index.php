<html>
<head data-version="Version v1.0.0-alpha.4 2022-11-25 16:02:54 UTC"> <!-- set by pre-commit -->
	<title>Quiz</title>

	<style>
		div {
			width: 100%;
		}
		
		button {
			text-align: center;
			font-size: 20px;
			width: 95%;
			margin: 10px 2.5%;
			padding: 0px;
			cursor: pointer;
		}
		.menuButton {
			width: 100%;
			margin: 0px;
		}
		.answerButton {
			height: 5vw;
		}
		.letterButton {
			height: 8vw;
		}

		.menuTd {
			padding: 1%
		}
		
		input {
			font-size: 20px;
			width: 95%;
			margin: 10px 2.5%;
			padding: 0px;
		}

		textarea {
			width: 100%;
			height: 20em;
		}
		
		h1 {
			text-align: center;
			width: 100%;
		}
		h2 {
			text-align: center;
			width: 100%;
			margin-block-start: 0.25em;
			margin-block-end: 0.25em;
		}
		h3 {
			text-align: center;
			width: 100%;
			margin-block-start: 0.3em;
			margin-block-end: 0.3em;
		}
		h4 {
			text-align: center;
			width: 100%;
			margin-block-start: 0.4em;
			margin-block-end: 0.4em;
		}
		h5 {
			text-align: center;
			width: 100%;
			margin-block-start: 0.5em;
			margin-block-end: 0.5em;
		}
		h6 {
			text-align: center;
			width: 100%;
			margin-block-start: 0.65em;
			margin-block-end: 0.65em;
		}
	</style>
	
	<script src="https://textfit.strml.net/examples/textFit.js"></script>
</head>
<body>

	<div id="variables"></div>

	<!-- "globals" -->
	<script>
		const BEGUN    = 5000000;
		const MASTERED =  100000;
		
		const lettersLetters = 7;
		
		const modeList = [
			{ name: "TRUE_FALSE", weight: 1.1, probFunc: function(x) {
				if (x <= 4900000) return (0.0000118792037493*1.00000237726**(1.00002886058*x) + 0.0484599092371) / (1 + Math.exp((x-4280000)/139000));
				if (x < 5100000) return 0;
				return x/10000000 - .5;
			} },
			{ name: "MULTIPLE_CHOICE", weight: 1.2, probFunc: function(x) {
				return (0.00143777949286*1.00000129498**(0.999999999351*x) + 0.068712710965) / (1 + Math.exp((x-5400000)/267000))
					+ Math.max((-x/10000000 + 1.5) / (1 + Math.exp((5400000-x)/267000)), 0);
			} },
			{ name: "MATCHING", weight: 1.15, probFunc: function(x) {
				if (x <= 4800000) return 4* (0.00169055154464*1.00000126005**(1.00000036691*x) + 0.046476040631) / (1 + Math.exp((x-4370000)/139000));
				return 0;
			} },
			{ name: "SYLABLES", weight: 1.3, probFunc: function(x) {
				if (x <= 4500000) return (0.0512206359603*1.00000092489**(0.697445260373*x) + 0.0344280040496) / (1 + Math.exp((x-3900000)/139000));
				return 0;
			} },
			{ name: "LETTERS", weight: 1.7, probFunc: function(x) {
				if (x <= 3700000) return (-0.248136529594*1.00000332632**(-0.375173221214*x)+0.327403990272) / (1 + Math.exp((x-3900000)/300000))
					- Math.exp(-(((x-3120000)/600000)**2)) * (x-3120000) / 1700000;
				return 0;
			} },
			{ name: "TEXT", weight: 2, probFunc: function(x) {
				if (x <= 2900000) return (145855.62689/(x+349629.950477) + 0.348277639381) / (1 + Math.exp((x-2537083.91759)/204000));
				return 0;
			} }
		];
		function getModeWeight(mode) {
			for (let modeObj of modeList)
				if (modeObj.name == mode)
					return modeObj.weight;
			return 1;
		}
		
		
		function getVar(name) {
			const attr = document.getElementById("variables").getAttribute(name);
			return attr ? attr : "";
		}
		
		function setVar(name, value) {
			document.getElementById("variables").setAttribute(name, value);
		}
		
		function saveElementState(element) {
			let e = document.getElementById(element);
			e.setAttribute("data-old-state", e.innerHTML);
		}
		
		function loadOldElementState(element) {
			let restoreElement = document.getElementById(element);
			restoreElement.innerHTML = restoreElement.getAttribute("data-old-state");
		}
		
		function exportSave(content, fileName) {
			var a = document.createElement("a");
			var file = new Blob([content], {type: 'text/plain'});
			a.href = URL.createObjectURL(file);
			a.download = fileName;
			a.click();
			URL.revokeObjectURL(a.href)
		}

		async function serverSave() {
			var pswd;
			await fetch("index.php", {
				method: "POST",
				headers: {
					"Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
				},
				body: `data=${localStorage.getItem("drops-data")}&key=${localStorage.getItem("drops-password")}`,
			})
				.then((response) => response.text())
				.then((res) => pswd = res.split("\n").slice(-1)[0]);
			if (pswd == "haha") {
				localStorage.setItem("drops-password", "haha");
				location.reload();
			}
		}

		function save() {
			localStorage.setItem("drops-data", "[ " + getVar("savedGroups") + " ]");
			serverSave();
		}

		async function addSecureKey(password) {
			await fetch("index.php", {
				method: "POST",
				headers: {
					"Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
				},
				body: `pswdCheck=${password}`,
			})
				.then((response) => response.text())
				.then((res) => localStorage.setItem("drops-password", res.split("\n").slice(-1)[0]));
		}

		async function removeSecureKey() {
			await fetch("index.php", {
				method: "POST",
				headers: {
					"Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
				},
				body: `rmKey=${localStorage.getItem("drops-password")}`,
			});
		}

		function versionCompare(local, github) {
			local  = local .split(" ")[1];
			local  = local .split([/[.-]/]);
			github = github.split([/[.-]/]);
			if (local[0] <  github[0]) { return true; }
			if (local[0] == github[0]) {
				if (local[1] <  github[1]) { return true; }
				if (local[1] == github[1]) {
					if (local[2] <  github[2]) { return true; }
					if (local[2] == github[2]) {
						if (local[3] && !github[3]) { return true; }
						if (local[3] <   github[3]) { return true; }
						if (local[3] ==  github[3]) {
							if (local[4] <  github[4]) { return true; }
						}
					}
				}
			}
			return false;
		}
	</script>

	<!-- startup -->
	<script>
		async function startup() {
			var localVersion = document.getElementsByTagName("head")[0].getAttribute("data-version");
			var githubVersion;
			await fetch("https://raw.githubusercontent.com/codeBodger/Quiz/main/README.md")
				.then((response) => response.text())
				.then((data) => githubVersion = data.split("\n")[0]);
	
			var releases;
			await fetch("https://raw.githubusercontent.com/codeBodger/Quiz/main/.releases")
				.then((response) => response.text())
				.then((data) => releases = data.split("\n"));
		
			if (localVersion != githubVersion)
				document.getElementById("logoutButton").parentElement.insertAdjacentHTML("afterend",
					`<tr><td colspan="2"><h4>There's a new unstable release!</h4></td></tr>
	 	<tr><td colspan="2"><h3>Check README.md for help about updates.</h3></td></tr>`
				);
			if (versionCompare(localVersion, releases[1] || ""))
				document.getElementById("logoutButton").parentElement.insertAdjacentHTML("afterend",
					`<tr><td colspan="2"><h3>There's a new alpha release!</h4></td></tr>`
				);
			if (versionCompare(localVersion, releases[2] || ""))
				document.getElementById("logoutButton").parentElement.insertAdjacentHTML("afterend",
					`<tr><td colspan="2"><h2>There's a new beta release!</h4></td></tr>`
				);
			if (versionCompare(localVersion, releases[3] || ""))
				document.getElementById("logoutButton").parentElement.insertAdjacentHTML("afterend",
					`<tr><td colspan="2"><h1>There's a new full release!</h4></td></tr>`
				);
			
			if (localStorage.getItem("drops-password")) {
				if (localStorage.getItem("drops-password") != "haha") {
					document.getElementById("loginButton" ).remove();
					document.getElementById("logoutButton").setAttribute("colspan", "2");
				}
				else {
					document.getElementById("loginButton" ).getElementsByTagName("button")[0].style.color = 'red';
					document.getElementById("loginButton" ).getElementsByTagName("button")[0].style.backgroundColor = 'ffaaaa';
					document.getElementById("logoutButton").parentElement.insertAdjacentHTML("afterend",
						`<tr><td colspan="2"><h3 style="color: red;">Login Failed!!</h3></td></tr>`
					);
				}
			}
			else {
				document.getElementById("logoutButton").remove();
				document.getElementById("loginButton" ).setAttribute("colspan", "2");
			}
	
			await fetch("data.json")
				.then((response) => response.json())
				.then((data) => localStorage.setItem("drops-data", JSON.stringify(data)));
			setVar("savedGroups", localStorage.getItem("drops-data").slice(1, -1));
	
			document.getElementById("loading").remove();
			document.getElementById("mainMenue").style.visibility = 'visible';
		}
	</script>

	<!-- set, group, and term functions -->
	<script>
		function groupLength(group) {
			return group.sets.length;
		}
		
		function groupUnlocked(group) {
			var x = 0;
			for (let s of group.sets)
				x = x + setUnlocked(s);
			return x;
		}
		
		function getRandomUnlockedFromGroup(group, except1 = null, except2 = null, except3 = null, except4 = null) { //Doesn't ensure unique
			var unlocked = [];
			for (let set of group.sets)
				for (let term of set.terms)
					if (!!term.progress && ![except1?.def, except2?.def, except3?.def, except4?.def].includes(term.def))
						unlocked.push(term);
			return unlocked[Math.floor(Math.random()*unlocked.length)];
		}
		
		function groupTermLetter(group) {
			let letters = [];
			for (let set of group.sets)
				for (let term of set.terms)
					for (char of term.term)
						if (!letters.includes(char.toLocaleUpperCase()) && char.toLocaleUpperCase() != char.toLocaleLowerCase())
							letters.push(char.toLocaleUpperCase());
			return letters[Math.floor(Math.random()*letters.length)];
		}
		
		function setMastery(set) {
			var d = setLength(set);
			var m = 0;
			for (t of set.terms)
				m = m + termMastery(t);
			return m/d;
		}
		
		function setUnlocked(set) {
			var x = 0;
			for (let t of set.terms)
				x = x + !!t.progress;
			return x;
		}
		
		function setLength(set) {
			return set.terms.length;
		}
		
		function termMastery(term) { //I might want to change these parameters
			if (term.progress == 0)
				return 0;
			if (term.progress < MASTERED)
				return 1;
			return (Math.log(BEGUN)-Math.log(term.progress))/Math.log(BEGUN/MASTERED);
		}
		
		function termMode(term) {
			let probs = [];
			for (let i = 0; i < modeList.length; i++) {
				probs[i] = modeList[i].probFunc(term.progress) + (probs[i-1] || 0);
			}
			if (probs[probs.length-1] == Infinity)
				return modeList[0].name;
			let rand = Math.random() * probs[probs.length-1];
			for (let i = 0; i < probs.length; i++) {
				if (probs[i] > rand)
					return modeList[i].name;
			}
			return modeList[modeList.length-1].name;
		}
		
		function nextChar(termT) {
			let ans = document.getElementById("lettersAnswer").innerHTML;
			let out = termT.charAt(ans.length -1);
			if (out.toLocaleUpperCase() == out.toLocaleLowerCase()) {
				document.getElementById("lettersAnswer").innerHTML = ans.slice(0,-1) + out + "|";
				return nextChar(termT);
			}
			return out;
		}
	</script>
	
	<!-- Studying a group -->
	<script>
		var matching = [];
		
		function StudyGroup() {
			document.getElementById("mainMenue").style.visibility = 'hidden';
			
			loadOldElementState("studyGroup");
			document.getElementById("studyGroup").style.visibility = 'visible';
			
			let thisInnerHTML = "";
			let groups = JSON.parse("[ " + getVar("savedGroups") + " ]");
			for (let g of groups) {
				let name = g.name;
				g = JSON.stringify(g);
				thisInnerHTML = thisInnerHTML + `<br><button class='studyButton' onclick='StuGroup(\`${g}\`, "${name}");'>${name}</button>`;
			}
			
			document.getElementById("studyWindow").innerHTML = thisInnerHTML;
		}
		
		function StuGroup(group, name) {
			document.getElementById("studyGroup").style.height = '100%';
			document.getElementById("studyGroup").getElementsByTagName("h1")[0].style.visibility = 'hidden';
			
			setVar("stuGroup", group);
			
			GetStuTerm();
		}
		
		function GetStuTerm() {
			let group = JSON.parse(getVar("stuGroup"));
			
			let termBin = [];
			let lockedTermBin = [];
			
			// Find the last set with any unlocked terms, or the set after the last fully mastered set
			var activeSet;
			for (let i = groupLength(group)-1; i >= 0; i--) { //This one gets the last partially unlocked, unless all are either completely mastered or none have been started
				if (setMastery(group.sets[i]) > 0 && setMastery(group.sets[i]) < 1) {
					activeSet = group.sets[i];
					break;
				}
			}
			if (!activeSet) //This one gets the first not started if the previous one failed
				for (let i = 0; i < groupLength(group); i++) {
					if (setMastery(group.sets[i]) == 0) {
						activeSet = group.sets[i];
						break;
					}
				}
			
			// Fill termBin and lockedTermBin from the sets before activeSet
			for (let set of group.sets) {
				if (set.name != activeSet?.name) {
					for (let term of set.terms) {
						if (term.progress) termBin.push(term);
						else         lockedTermBin.push(term);
					}
				}
				else break;
			}
			
			// If there is a set that's in progress:
			if (activeSet) {
				// console.log(setMastery(activeSet) + "\t" + group.consecutiveCorrect + "\t" + setUnlocked(activeSet) + "\t" + setLength(activeSet));
				if ((group.consecutiveCorrect >= 3*Math.exp(setMastery(activeSet)) || groupUnlocked(group) < 5) && setUnlocked(activeSet) < setLength(activeSet)) {
					var i;
					var weights = [];
					for (i = 0; i < setLength(activeSet) + lockedTermBin.length; i++) {
						weights[i] = !activeSet.terms[i]?.progress + (weights[i-1] || 0);
					}
					var random = Math.random() * weights[weights.length-1];
					for (i = 0; i < weights.length; i++)
						if(weights[i] > random)
							break;
					StuTerm(activeSet.terms[i] || lockedTermBin[i-setLength(activeSet)], activeSet.name, group.name, "NEW_TERM", setMastery(activeSet));
					return;
				}
				else {
					var i;
					var weights = [];
					for (i = 0; i < setLength(activeSet) + termBin.length; i++) {
						weights[i] = (activeSet.terms[i]?.progress || termBin[i-setLength(activeSet)]?.progress/5 || 0) + (weights[i-1] || 0);
					}
					var random = Math.random() * weights[weights.length-1];
					for (i = 0; i < weights.length; i++)
						if(weights[i] > random)
							break;
					StuTerm(activeSet.terms[i] || termBin[i-setLength(activeSet)], activeSet.name, group.name, termMode(activeSet.terms[i] || termBin[i-setLength(activeSet)]), setMastery(activeSet));
					return;
				}
			}
			
			// Since there's no set in progress:
			var i;
			var weights = [];
			for (i = 0; i < termBin.length; i++) {
				weights[i] = termBin[i].progress + (weights[i-1] || 0);
			}
			var random = Math.random() * weights[weights.length-1];
			for (i = 0; i < weights.length; i++)
				if(weights[i] > random)
					break;
			StuTerm(termBin[i], "Mastered!  Great Job!", group.name, termMode(termBin[i]), 1);
		}
		
		function StuTerm(term, setName, groupName, mode, mastery) {
			console.log(mode);
			modeSwitch: switch(mode) {
				case "NEW_TERM":
					document.getElementsByTagName("body")[0].style.backgroundColor = '#f07f10';
					document.getElementById("studyWindow").innerHTML = `
						<h3>Group: ${groupName}</h3>
						<h5>Set: ${setName}</h5>
						<h6>Mastery: ${String(mastery*100).slice(0,5)}%</h6>
						<br><br>
						<h4>Definition:</h4>
						<h2>${term.def}</h2>
						<br>
						<h4>Term:</h4>
						<h2>${term.term}</h2>
						<button class="answerButton" style="color: green; background-color: #aaffaa; width: 44.5%;" onclick='CheckAnswer("CONFIDENT", "${term.def}", "${setName}", "${groupName}")'>Confident</button>
						<button class="answerButton" style="color: red;   background-color: #ffaaaa; width: 44.5%;" onclick='CheckAnswer("NEEDS_WORK", "${term.def}", "${setName}", "${groupName}")'>Needs Work</button>
					`;
				break;
				
				case "TRUE_FALSE": try {
					document.getElementsByTagName("body")[0].style.backgroundColor = '#227ad3';
					let correctTerm = term.term;
					if (Math.random() < 0.5) {
						correctTerm = getRandomUnlockedFromGroup(JSON.parse(getVar("stuGroup")), term).term;
					}
					document.getElementById("studyWindow").innerHTML = `
						<h3>Group: ${groupName}</h3>
						<h5>Set: ${setName}</h5>
						<h6>Mastery: ${String(mastery*100).slice(0,5)}%</h6>
						<br><br>
						<h4>Definition:</h4>
						<h2>${term.def}</h2>
						<br>
						<h4>Term:</h4>
						<h2>${correctTerm}</h2>
						<button class="answerButton" style="color: green; background-color: #aaffaa; width: 44.5%;" onclick='CheckAnswer("TRUE_FALSE_${(correctTerm==term.term)}", "${term.def}", "${setName}", "${groupName}")'>True</button>
						<button class="answerButton" style="color: red;   background-color: #ffaaaa; width: 44.5%;" onclick='CheckAnswer("TRUE_FALSE_${(correctTerm!=term.term)}", "${term.def}", "${setName}", "${groupName}")'>False</button>
					`;
				break modeSwitch; } catch {}
				
				case "MULTIPLE_CHOICE": try {
					document.getElementsByTagName("body")[0].style.backgroundColor = '#0cb2b5';
					let terms = [];
					let correctTerm = Math.floor(Math.random() * 4);
					terms[0] = getRandomUnlockedFromGroup(JSON.parse(getVar("stuGroup")), term).term;
					terms[1] = getRandomUnlockedFromGroup(JSON.parse(getVar("stuGroup")), term, terms[0]).term;
					terms[2] = getRandomUnlockedFromGroup(JSON.parse(getVar("stuGroup")), term, terms[0], terms[1]).term;
					terms[3] = getRandomUnlockedFromGroup(JSON.parse(getVar("stuGroup")), term, terms[0], terms[1], terms[2]).term;
					terms[correctTerm] = term.term;
					document.getElementById("studyWindow").innerHTML = `
						<h3>Group: ${groupName}</h3>
						<h5>Set: ${setName}</h5>
						<h6>Mastery: ${String(mastery*100).slice(0,5)}%</h6>
						<br><br>
						<h4>Definition:</h4>
						<h2>${term.def}</h2>
						<br>
						<h2>Choose the Correct Term:</h2>
						<button class="answerButton" style="width: 44.5%;" onclick='CheckAnswer("MULTIPLE_CHOICE_${(terms[0]==term.term)}", "${term.def}", "${setName}", "${groupName}")'>${terms[0]}</button>
						<button class="answerButton" style="width: 44.5%;" onclick='CheckAnswer("MULTIPLE_CHOICE_${(terms[1]==term.term)}", "${term.def}", "${setName}", "${groupName}")'>${terms[1]}</button>
						<button class="answerButton" style="width: 44.5%;" onclick='CheckAnswer("MULTIPLE_CHOICE_${(terms[2]==term.term)}", "${term.def}", "${setName}", "${groupName}")'>${terms[2]}</button>
						<button class="answerButton" style="width: 44.5%;" onclick='CheckAnswer("MULTIPLE_CHOICE_${(terms[3]==term.term)}", "${term.def}", "${setName}", "${groupName}")'>${terms[3]}</button>
					`;
				break modeSwitch; } catch {}
				
				case "MATCHING":
					document.getElementsByTagName("body")[0].style.backgroundColor = '#40b949';
					for (let t of matching) {
						if (t.term == term.term || t.def == term.def) {
							GetStuTerm();
							break modeSwitch;
						}
					}
					matching.push(term);
					if (matching.length < 3) {
						GetStuTerm();
						break;
					}
					let matchNum = [];
					let temp = Math.floor(Math.random()*2);
					matchNum.push(Math.floor(Math.random()*3));
					matchNum.push((!matchNum[0]+!temp+matchNum[0]*!temp) % 3 + Math.floor(matchNum[0]*temp/2));
					matchNum.push(3 - (matchNum[0]+matchNum[1]));
					document.getElementById("studyWindow").innerHTML = `
						<h3>Group: ${groupName}</h3>
						<h5>Set: ${setName}</h5>
						<h6>Mastery: ${String(mastery*100).slice(0,5)}%</h6>
						<br><br>
						<h2>Choose the Correct Pairs of Terms and Definitions</h2>
						<table style="width: 100%;">
							<tr>
								<th style="width: 50%;">Terms</th>
								<th style="width: 50%;">Definitions</th>
							</tr>
							<tr>
								<td><button class="answerButton" id="matchTerm0" onclick='CheckAnswer("MATCHING_Term0", "${matching[0].def}", "${setName}", "${groupName}")'>${matching[0].term}</button></td>
								<td><button class="answerButton" id="matchDef${matchNum[0]}"  onclick='CheckAnswer("MATCHING_Def${matchNum[0]}", "${matching[matchNum[0]].def}", "${setName}", "${groupName}")'>${matching[matchNum[0]].def}</button></td>
							</tr>
							<tr>
								<td><button class="answerButton" id="matchTerm1" onclick='CheckAnswer("MATCHING_Term1", "${matching[1].def}", "${setName}", "${groupName}")'>${matching[1].term}</button></td>
								<td><button class="answerButton" id="matchDef${matchNum[1]}"  onclick='CheckAnswer("MATCHING_Def${matchNum[1]}", "${matching[matchNum[1]].def}", "${setName}", "${groupName}")'>${matching[matchNum[1]].def}</button></td>
							</tr>
							<tr>
								<td><button class="answerButton" id="matchTerm2" onclick='CheckAnswer("MATCHING_Term2", "${matching[2].def}", "${setName}", "${groupName}")'>${matching[2].term}</button></td>
								<td><button class="answerButton" id="matchDef${matchNum[2]}"  onclick='CheckAnswer("MATCHING_Def${matchNum[2]}", "${matching[matchNum[2]].def}", "${setName}", "${groupName}")'>${matching[matchNum[2]].def}</button></td>
							</tr>
						</table>
					`;
					matching = [];
				break;
				
				case "SYLABLES":
					document.getElementsByTagName("body")[0].style.backgroundColor = '#d10578';
				case "LETTERS":
					document.getElementsByTagName("body")[0].style.backgroundColor = '#ba03fc';
					
					document.getElementById("studyWindow").innerHTML = `
						<h3>Group: ${groupName}</h3>
						<h5>Set: ${setName}</h5>
						<h6>Mastery: ${String(mastery*100).slice(0,5)}%</h6>
						<br><br>
						<h4>Definition:</h4>
						<h2>${term.def}</h2>
						<br>
						<h4>Term:</h4>
						<h2 id="lettersAnswer">|</h2>
						<table id="letterButtons" style="width: 95%; margin-left: auto; margin-right: auto; table-layout: fixed;"></table>
					`;
					
					let buttons = [];
					for (let i = 0; i < lettersLetters; i++) buttons.push(groupTermLetter(JSON.parse(getVar("stuGroup"))));
					buttons[Math.floor(Math.random()*lettersLetters)] = nextChar(term.term).toLocaleUpperCase();
					
					let thisInnerHTML = "";
					for (let i = 0; i < lettersLetters; i++) thisInnerHTML = thisInnerHTML + `
							<td><button class="answerButton letterButton" onclick='setVar("letter","${buttons[i]}"); CheckAnswer("LETTERS", "${term.def}", "${setName}", "${groupName}")'>${buttons[i]}</button></td>`;
					document.getElementById("letterButtons").innerHTML = `<tr>${thisInnerHTML}</tr>`;
				break;
				
				case "TEXT":
					document.getElementsByTagName("body")[0].style.backgroundColor = '#7b03fc';
					document.getElementById("studyWindow").innerHTML = `
						<h3>Group: ${groupName}</h3>
						<h5>Set: ${setName}</h5>
						<h6>Mastery: ${String(mastery*100).slice(0,5)}%</h6>
						<br><br>
						<h4>Definition:</h4>
						<h2>${term.def}</h2>
						<br>
						<h4>Term:</h4>
						<input type="text" id="termInput">
						<button class="answerButton" style="font-size: 20px;" onclick='CheckAnswer("TEXT", "${term.def}", "${setName}", "${groupName}")'>Done</button>
					`;
				break;
			}
			
			textFit(document.getElementsByClassName("answerButton"));
		}
		
		function CheckAnswer(mode, termDef, setName, groupName) {
			let group = JSON.parse(getVar("stuGroup"));
			var trueTerm;
			
			switch(mode) {
				case "CONFIDENT":
					loop: for (let set of group.sets) {
						for (let term of set.terms) {
							if (term.def == termDef) {
								trueTerm = term.term;
								term.progress = MASTERED;
								group.consecutiveCorrect = 0;
								break loop;
							}
						}
					}
					document.getElementById("studyWindow").innerHTML = `
						<h3>Group: ${groupName}</h3>
						<h5>Set: ${setName}</h5>
						<h6>&nbsp;</h6>
						<br><br>
						<h4>Definition:</h4>
						<h2>${termDef}</h2>
						<br>
						<h4>Term:</h4>
						<h2>${trueTerm}</h2>
						<br>
						<h2 style="color:green;">You said that you were confident!  That's great!</h2>
						<button onclick="GetStuTerm()">Continue</button>
					`;
				break;
				case "NEEDS_WORK":
					loop: for (let set of group.sets) {
						for (let term of set.terms) {
							if (term.def == termDef) {
								trueTerm = term.term;
								term.progress = BEGUN;
								group.consecutiveCorrect = 0;
								break loop;
							}
						}
					}
					document.getElementById("studyWindow").innerHTML = `
						<h3>Group: ${groupName}</h3>
						<h5>Set: ${setName}</h5>
						<h6>&nbsp;</h6>
						<br><br>
						<h4>Definition:</h4>
						<h2>${termDef}</h2>
						<br>
						<h4>Term:</h4>
						<h2>${trueTerm}</h2>
						<br>
						<h2 style="color:red;">You said that you needed to work on this term!  That's OK; it's what you're here for!</h2>
						<button onclick="GetStuTerm()">Continue</button>
					`;
				break;
				
				case "TRUE_FALSE_true":
					loop: for (let set of group.sets) {
						for (let term of set.terms) {
							if (term.def == termDef) {
								trueTerm = term.term;
								term.progress = term.progress/getModeWeight("TRUE_FALSE");
								group.consecutiveCorrect = group.consecutiveCorrect+1;
								break loop;
							}
						}
					}
					document.getElementById("studyWindow").innerHTML = `
						<h3>Group: ${groupName}</h3>
						<h5>Set: ${setName}</h5>
						<h6>&nbsp;</h6>
						<br><br>
						<h4>Definition:</h4>
						<h2>${termDef}</h2>
						<br>
						<h4>Term:</h4>
						<h2>${trueTerm}</h2>
						<br>
						<h2 style="color:green;">Correct!</h2>
						<button onclick="GetStuTerm()">Continue</button>
					`;
				break;
				case "TRUE_FALSE_false":
					loop: for (let set of group.sets) {
						for (let term of set.terms) {
							if (term.def == termDef) {
								trueTerm = term.term;
								term.progress = term.progress*getModeWeight("TRUE_FALSE");
								group.consecutiveCorrect = 0;
								break loop;
							}
						}
					}
					document.getElementById("studyWindow").innerHTML = `
						<h3>Group: ${groupName}</h3>
						<h5>Set: ${setName}</h5>
						<h6>&nbsp;</h6>
						<br><br>
						<h4>Definition:</h4>
						<h2>${termDef}</h2>
						<br>
						<h4>Term:</h4>
						<h2>${trueTerm}</h2>
						<br>
						<h2 style="color:red;">Incorrect.</h2>
						<button onclick="GetStuTerm()">Continue</button>
					`;
				break;
				
				case "MULTIPLE_CHOICE_true":
					loop: for (let set of group.sets) {
						for (let term of set.terms) {
							if (term.def == termDef) {
								trueTerm = term.term;
								term.progress = term.progress/getModeWeight("MULTIPLE_CHOICE");
								group.consecutiveCorrect = group.consecutiveCorrect+1;
								break loop;
							}
						}
					}
					document.getElementById("studyWindow").innerHTML = `
						<h3>Group: ${groupName}</h3>
						<h5>Set: ${setName}</h5>
						<h6>&nbsp;</h6>
						<br><br>
						<h4>Definition:</h4>
						<h2>${termDef}</h2>
						<br>
						<h4>Term:</h4>
						<h2>${trueTerm}</h2>
						<br>
						<h2 style="color:green;">Correct!</h2>
						<button onclick="GetStuTerm()">Continue</button>
					`;
				break;
				case "MULTIPLE_CHOICE_false":
					loop: for (let set of group.sets) {
						for (let term of set.terms) {
							if (term.def == termDef) {
								trueTerm = term.term;
								term.progress = term.progress*getModeWeight("MULTIPLE_CHOICE");
								group.consecutiveCorrect = 0;
								break loop;
							}
						}
					}
					document.getElementById("studyWindow").innerHTML = `
						<h3>Group: ${groupName}</h3>
						<h5>Set: ${setName}</h5>
						<h6>&nbsp;</h6>
						<br><br>
						<h4>Definition:</h4>
						<h2>${termDef}</h2>
						<br>
						<h4>Term:</h4>
						<h2>${trueTerm}</h2>
						<br>
						<h2 style="color:red;">Incorrect.</h2>
						<button onclick="GetStuTerm()">Continue</button>
					`;
				break;
				
				case "MATCHING_Term0":
					if (getVar("matchDef") == "0") {
						document.getElementById("matchTerm0").style.visibility = 'hidden';
						document.getElementById("matchDef0").style.visibility = 'hidden';
						loop: for (let set of group.sets) {
							for (let term of set.terms) {
								if (term.def == termDef) {
									trueTerm = term.term;
									term.progress = term.progress/getModeWeight("MATCHING");
									group.consecutiveCorrect = group.consecutiveCorrect+1;
									break loop;
								}
							}
						}
					} else
					if (getVar("matchDef") == "") {
						document.getElementById("matchTerm0").style.backgroundColor = '#888888';
						setVar("matchTerm", "0");
					}
					else {
						document.getElementById("matchDef" + getVar("matchDef")).style.backgroundColor = null;
						loop: for (let set of group.sets) {
							for (let term of set.terms) {
								if (term.def == termDef) {
									trueTerm = term.term;
									term.progress = term.progress*getModeWeight("MATCHING");
									group.consecutiveCorrect = 0;
									break loop;
								}
							}
						}
					}
					setVar("matchDef","");
					if (document.getElementById("matchTerm0").style.visibility == 'hidden' &&
							document.getElementById("matchDef0" ).style.visibility == 'hidden' &&
							document.getElementById("matchTerm1").style.visibility == 'hidden' &&
							document.getElementById("matchDef1" ).style.visibility == 'hidden' &&
							document.getElementById("matchTerm2").style.visibility == 'hidden' &&
							document.getElementById("matchDef2" ).style.visibility == 'hidden'
					) document.getElementById("studyWindow").innerHTML = `<button onclick="GetStuTerm()">Continue</button>`;
				break;
				case "MATCHING_Def0":
					if (getVar("matchTerm") == "0") {
						document.getElementById("matchTerm0").style.visibility = 'hidden';
						document.getElementById("matchDef0").style.visibility = 'hidden';
						loop: for (let set of group.sets) {
							for (let term of set.terms) {
								if (term.def == termDef) {
									trueTerm = term.term;
									term.progress = term.progress/getModeWeight("MATCHING");
									group.consecutiveCorrect = group.consecutiveCorrect+1;
									break loop;
								}
							}
						}
					} else
					if (getVar("matchTerm") == "") {
						document.getElementById("matchDef0").style.backgroundColor = '#888888';
						setVar("matchDef", "0");
					}
					else {
						document.getElementById("matchTerm" + getVar("matchTerm")).style.backgroundColor = null;
						loop: for (let set of group.sets) {
							for (let term of set.terms) {
								if (term.def == termDef) {
									trueTerm = term.term;
									term.progress = term.progress*getModeWeight("MATCHING");
									group.consecutiveCorrect = 0;
									break loop;
								}
							}
						}
					}
					setVar("matchTerm","");
					if (document.getElementById("matchTerm0").style.visibility == 'hidden' &&
							document.getElementById("matchDef0" ).style.visibility == 'hidden' &&
							document.getElementById("matchTerm1").style.visibility == 'hidden' &&
							document.getElementById("matchDef1" ).style.visibility == 'hidden' &&
							document.getElementById("matchTerm2").style.visibility == 'hidden' &&
							document.getElementById("matchDef2" ).style.visibility == 'hidden'
					) document.getElementById("studyWindow").innerHTML = `<button onclick="GetStuTerm()">Continue</button>`;
				break;
				case "MATCHING_Term1":
					if (getVar("matchDef") == "1") {
						document.getElementById("matchTerm1").style.visibility = 'hidden';
						document.getElementById("matchDef1").style.visibility = 'hidden';
						loop: for (let set of group.sets) {
							for (let term of set.terms) {
								if (term.def == termDef) {
									trueTerm = term.term;
									term.progress = term.progress/getModeWeight("MATCHING");
									group.consecutiveCorrect = group.consecutiveCorrect+1;
									break loop;
								}
							}
						}
					} else
					if (getVar("matchDef") == "") {
						document.getElementById("matchTerm1").style.backgroundColor = '#888888';
						setVar("matchTerm", "1");
					}
					else {
						document.getElementById("matchDef" + getVar("matchDef")).style.backgroundColor = null;
						loop: for (let set of group.sets) {
							for (let term of set.terms) {
								if (term.def == termDef) {
									trueTerm = term.term;
									term.progress = term.progress*getModeWeight("MATCHING");
									group.consecutiveCorrect = 0;
									break loop;
								}
							}
						}
					}
					setVar("matchDef","");
					if (document.getElementById("matchTerm0").style.visibility == 'hidden' &&
							document.getElementById("matchDef0" ).style.visibility == 'hidden' &&
							document.getElementById("matchTerm1").style.visibility == 'hidden' &&
							document.getElementById("matchDef1" ).style.visibility == 'hidden' &&
							document.getElementById("matchTerm2").style.visibility == 'hidden' &&
							document.getElementById("matchDef2" ).style.visibility == 'hidden'
					) document.getElementById("studyWindow").innerHTML = `<button onclick="GetStuTerm()">Continue</button>`;
				break;
				case "MATCHING_Def1":
					if (getVar("matchTerm") == "1") {
						document.getElementById("matchTerm1").style.visibility = 'hidden';
						document.getElementById("matchDef1").style.visibility = 'hidden';
						loop: for (let set of group.sets) {
							for (let term of set.terms) {
								if (term.def == termDef) {
									trueTerm = term.term;
									term.progress = term.progress/getModeWeight("MATCHING");
									group.consecutiveCorrect = group.consecutiveCorrect+1;
									break loop;
								}
							}
						}
					} else
					if (getVar("matchTerm") == "") {
						document.getElementById("matchDef1").style.backgroundColor = '#888888';
						setVar("matchDef", "1");
					}
					else {
						document.getElementById("matchTerm" + getVar("matchTerm")).style.backgroundColor = null;
						loop: for (let set of group.sets) {
							for (let term of set.terms) {
								if (term.def == termDef) {
									trueTerm = term.term;
									term.progress = term.progress*getModeWeight("MATCHING");
									group.consecutiveCorrect = 0;
									break loop;
								}
							}
						}
					}
					setVar("matchTerm","");
					if (document.getElementById("matchTerm0").style.visibility == 'hidden' &&
							document.getElementById("matchDef0" ).style.visibility == 'hidden' &&
							document.getElementById("matchTerm1").style.visibility == 'hidden' &&
							document.getElementById("matchDef1" ).style.visibility == 'hidden' &&
							document.getElementById("matchTerm2").style.visibility == 'hidden' &&
							document.getElementById("matchDef2" ).style.visibility == 'hidden'
					) document.getElementById("studyWindow").innerHTML = `<button onclick="GetStuTerm()">Continue</button>`;
				break;
				case "MATCHING_Term2":
					if (getVar("matchDef") == "2") {
						document.getElementById("matchTerm2").style.visibility = 'hidden';
						document.getElementById("matchDef2").style.visibility = 'hidden';
						loop: for (let set of group.sets) {
							for (let term of set.terms) {
								if (term.def == termDef) {
									trueTerm = term.term;
									term.progress = term.progress/getModeWeight("MATCHING");
									group.consecutiveCorrect = group.consecutiveCorrect+1;
									break loop;
								}
							}
						}
					} else
					if (getVar("matchDef") == "") {
						document.getElementById("matchTerm2").style.backgroundColor = '#888888';
						setVar("matchTerm", "2");
					}
					else {
						document.getElementById("matchDef" + getVar("matchDef")).style.backgroundColor = null;
						loop: for (let set of group.sets) {
							for (let term of set.terms) {
								if (term.def == termDef) {
									trueTerm = term.term;
									term.progress = term.progress*getModeWeight("MATCHING");
									group.consecutiveCorrect = 0;
									break loop;
								}
							}
						}
					}
					setVar("matchDef","");
					if (document.getElementById("matchTerm0").style.visibility == 'hidden' &&
							document.getElementById("matchDef0" ).style.visibility == 'hidden' &&
							document.getElementById("matchTerm1").style.visibility == 'hidden' &&
							document.getElementById("matchDef1" ).style.visibility == 'hidden' &&
							document.getElementById("matchTerm2").style.visibility == 'hidden' &&
							document.getElementById("matchDef2" ).style.visibility == 'hidden'
					) document.getElementById("studyWindow").innerHTML = `<button onclick="GetStuTerm()">Continue</button>`;
				break;
				case "MATCHING_Def2":
					if (getVar("matchTerm") == "2") {
						document.getElementById("matchTerm2").style.visibility = 'hidden';
						document.getElementById("matchDef2").style.visibility = 'hidden';
						loop: for (let set of group.sets) {
							for (let term of set.terms) {
								if (term.def == termDef) {
									trueTerm = term.term;
									term.progress = term.progress/getModeWeight("MATCHING");
									group.consecutiveCorrect = group.consecutiveCorrect+1;
									break loop;
								}
							}
						}
					} else
					if (getVar("matchTerm") == "") {
						document.getElementById("matchDef2").style.backgroundColor = '#888888';
						setVar("matchDef", "2");
					}
					else {
						document.getElementById("matchTerm" + getVar("matchTerm")).style.backgroundColor = null;
						loop: for (let set of group.sets) {
							for (let term of set.terms) {
								if (term.def == termDef) {
									trueTerm = term.term;
									term.progress = term.progress*getModeWeight("MATCHING");
									group.consecutiveCorrect = 0;
									break loop;
								}
							}
						}
					}
					setVar("matchTerm","");
					if (document.getElementById("matchTerm0").style.visibility == 'hidden' &&
							document.getElementById("matchDef0" ).style.visibility == 'hidden' &&
							document.getElementById("matchTerm1").style.visibility == 'hidden' &&
							document.getElementById("matchDef1" ).style.visibility == 'hidden' &&
							document.getElementById("matchTerm2").style.visibility == 'hidden' &&
							document.getElementById("matchDef2" ).style.visibility == 'hidden'
					) document.getElementById("studyWindow").innerHTML = `<button onclick="GetStuTerm()">Continue</button>`;
				break;
				
				case "LETTERS":
					var entered = document.getElementById("lettersAnswer").innerHTML;
					var letterEntered = getVar("letter");
					loop: for (let set of group.sets) {
						for (let term of set.terms) {
							if (term.def == termDef) {
								trueTerm = term.term;
								if (nextChar(term.term).toLocaleUpperCase() == letterEntered) {
									term.progress = term.progress/Math.pow(getModeWeight("LETTERS"), 1 - 1/term.term.length);
									group.consecutiveCorrect = group.consecutiveCorrect + 1/term.term.length;
									document.getElementById("lettersAnswer").innerHTML = entered.slice(0,-1) + nextChar(term.term) + "|";
								}
								else {
									term.progress = term.progress*Math.pow(getModeWeight("LETTERS"), 1 - 1/term.term.length);
									group.consecutiveCorrect = group.consecutiveCorrect % 1;
									for (let lB of document.getElementsByClassName("letterButton"))
										if (lB.innerHTML == letterEntered) lB.style.backgroundColor = '#ffaaaa';
									return;
								}
								break loop;
							}
						}
					}
					
					let letAns = document.getElementById("lettersAnswer").innerHTML;
					let nextCharVar = trueTerm[letAns.length -1];
					while (nextCharVar && nextCharVar.toLocaleUpperCase() == nextCharVar.toLocaleLowerCase()) {
						document.getElementById("lettersAnswer").innerHTML = letAns.slice(0,-1) + nextCharVar + "|";
						letAns = document.getElementById("lettersAnswer").innerHTML;
						nextCharVar = trueTerm[letAns.length -1];
					}
					
					if (document.getElementById("lettersAnswer").innerHTML.length == trueTerm.length +1) {
						document.getElementById("studyWindow").innerHTML = `
							<h3>Group: ${groupName}</h3>
							<h5>Set: ${setName}</h5>
							<h6>&nbsp;</h6>
							<br><br>
							<h4>Definition:</h4>
							<h2>${termDef}</h2>
							<br>
							<h4>Term:</h4>
							<h2>${trueTerm}</h2>
							<br>
							<button onclick="GetStuTerm()">Continue</button>
						`;
						group.consecutiveCorrect = group.consecutiveCorrect+1;
					}
					else {
						let buttons = [];
						for (let i = 0; i < lettersLetters; i++) buttons.push(groupTermLetter(JSON.parse(getVar("stuGroup"))));
						buttons[Math.floor(Math.random()*lettersLetters)] = nextChar(trueTerm).toLocaleUpperCase();
						
						let thisInnerHTML = "";
						for (let i = 0; i < lettersLetters; i++) thisInnerHTML = thisInnerHTML + `
							<td><button class="letterButton answerButton" onclick='setVar("letter","${buttons[i]}"); CheckAnswer("LETTERS", "${termDef}", "${setName}", "${groupName}")'>${buttons[i]}</button></td>`;
						document.getElementById("letterButtons").innerHTML = `<tr>${thisInnerHTML}</tr>`;
						textFit(document.getElementsByClassName("answerButton"));
					}
				break;
				
				case "TEXT":
					var entered = document.getElementById("termInput").value;
					var correct;
					loop: for (let set of group.sets) {
						for (let term of set.terms) {
							if (term.def == termDef) {
								trueTerm = term.term;
								if (term.term == entered) {
									correct = true;
									term.progress = term.progress/getModeWeight("TEXT");
									group.consecutiveCorrect = group.consecutiveCorrect+1 || 1;
								}
								else {
									correct = false;
									term.progress = term.progress*getModeWeight("TEXT");
									group.consecutiveCorrect = 0;
								}
								break loop;
							}
						}
					}
			
					document.getElementById("studyWindow").innerHTML = `
						<h3>Group: ${groupName}</h3>
						<h5>Set: ${setName}</h5>
						<h6>&nbsp;</h6>
						<br><br>
						<h4>Definition:</h4>
						<h2>${termDef}</h2>
						<br>
						<h4>Term:</h4>
						<h4>You said:</h4>
						<h2>&nbsp;${entered}&nbsp;</h2>
						<h4>Answer:</h4>
						<h2>${trueTerm}</h2>
						<br>
						<h2 style="color: ${(correct ? "green" : "red")};">${(correct ? "Correct!" : "Incorrect.")}</h2>
						<button onclick="GetStuTerm()">Continue</button>
					`;
				break;
			}
			
			setVar("stuGroup", JSON.stringify(group));
			
			let groups = JSON.parse("[ " + getVar("savedGroups") + " ]");
			for (let i = 0; i<groups.length; i++) {
				if (groups[i].name == groupName) {
					groups[i] = group;
					break;
				}
			}
			setVar("savedGroups", JSON.stringify(groups).slice(1, -1));
			save();
		}
		
		function FinishStudy() { location.reload(); }
	</script>
	
	<!-- Creating a group -->
	<script>
		function NewGroup() {
			document.getElementById("mainMenue").style.visibility = 'hidden';
			document.getElementById("newGroup").style.visibility = 'visible';
			document.getElementById("newGroupName").focus();
		}
		
		function NewSet() {
			document.getElementById("newGroup").style.visibility = 'hidden';
			document.getElementById("newTerms").style.visibility = 'hidden';
			document.getElementById("newSet").style.visibility = 'visible';
			document.getElementById("newSetName").focus();
		}
		
		function NewTerms() {
			document.getElementById("newSet").style.visibility = 'hidden';
			
			loadOldElementState("newTerms");
			document.getElementById("newTerms").style.visibility = 'visible';
			
			document.getElementsByClassName("term")[0].focus();
		}
		
		function NewTerm() {
			document.getElementById("currentTerm").removeAttribute("id");
			
			SaveGroup();
			
			document.getElementById("newTermButtonRow").insertAdjacentHTML(
				"beforebegin",
				`
					<tr class="termPair">
						<td><input type="text" class="term" id="currentTerm"></td>
						<td><input type="text" class="def"></td>
					</tr>
				`
			);
			
			document.getElementById("currentTerm").focus();
		}
		
		function SaveGroup() {
			SaveSet();
			
			const groupName = document.getElementById("newGroupName").value;
			const sets = getVar("sets");
			
			let toJSON = '{ "name":"' + groupName + '", "sets":[ ' + sets + " ] }";
			
			let savedGroups = getVar("savedGroups");
			let comma = savedGroups.length != 0 ? ", " : "";
			setVar("groups", (savedGroups + comma + toJSON).replace(/'/g, "’"));
			
			save();
		}
		
		function SaveSet() {
			const setName = document.getElementById("newSetName").value;
			const termTable = document.getElementById("newTerms");
			const termRows = termTable.getElementsByClassName("termPair");
			
			let toJSON = '{ "name":"' + setName + '", "terms":[ ';

			let term = termRows[0].getElementsByClassName("term")[0].value;
			let def = termRows[0].getElementsByClassName("def")[0].value;
			toJSON = toJSON + '{ "term":"' + term + '", "def":"' + def + '", "progress":0}';

			for (let i = 1; i < termRows.length; i++) {
				term = termRows[i].getElementsByClassName("term")[0].value;
				def = termRows[i].getElementsByClassName("def")[0].value;
				toJSON = toJSON + ', { "term":"' + term + '", "def":"' + def + '", "progress":0}';
			}
			
			toJSON = toJSON + ' ] }'
			
			let savedSets = getVar("savedSets");
			let comma = savedSets.length != 0 ? ", " : "";
			setVar("sets", savedSets + comma + toJSON);
		}
		
		function FinishSet() {
			SaveGroup();
			setVar("savedSets", getVar("sets"));
			
			NewSet();
		}
		
		function FinishGroup() {
			setVar("savedSets", "");
			setVar("sets", "");
			setVar("savedGroups", getVar("groups"));
			
			location.reload();
		}
	</script>
	
	<!-- Editing a group -->
	<script>
		function EditGroup() {
			
		}
	</script>
	
	<!-- Editing a group as text -->
	<script>
		function EditAsText() {
			document.getElementById("mainMenue").style.visibility = 'hidden';
			
			loadOldElementState("editAsText");
			document.getElementById("editAsText").style.visibility = 'visible';
			
			let thisInnerHTML = "";
			let groups = JSON.parse("[ " + getVar("savedGroups") + " ]");
			for (let g of groups) {
				let name = g.name;
				g = JSON.stringify(g);
				g = g.replace(/"/g, "&quot;");
				g = g.replace(/'/g, "&#39;");
				thisInnerHTML = thisInnerHTML + `<br><button class='editButton' onclick='GroupToEditAsText(\`${g}\`);'>${name}</button>`;
			}
			
			document.getElementById("editAsText").getElementsByTagName("h1")[0].insertAdjacentHTML("afterend", thisInnerHTML);
		}
		
		function GroupToEditAsText(group) {
			group = JSON.parse(group);
			
			setVar("groups", "");
			for (let g of JSON.parse("[" + getVar("savedGroups") + "]"))
				if (g.name != group.name)
					setVar("groups", getVar("groups") + JSON.stringify(g) + ", ");
			setVar("groups", getVar("groups").slice(0,-2));
			setVar("savedGroups", getVar("groups"));
			
			let textGroup = group.name + "\t\n";
			for (let set of group.sets) {
				textGroup = textGroup + set.name + "\t\n";
				for (let term of set.terms)
					textGroup = textGroup + term.term + "\t" + term.def + "\t" + term.progress + "\n";
				textGroup = textGroup + "\t\n";
			}
			textGroup = textGroup.slice(0,-2);
			
			document.getElementById("editAsText").style.visibility = 'hidden';
			document.getElementById("editingAsText").getElementsByTagName("textarea")[0].value = textGroup;
			document.getElementById("editingAsText").style.visibility = 'visible';
		}
		
		function FinishEditAsText() {
			let newGroup = '';
			
			let group = document.getElementById("editingAsText").getElementsByTagName("textarea")[0].value.replace(/\t\n/g, "\n");
			group = group.replace("\n", "\n\n\n");
			group = group.split("\n\n\n");
			newGroup = newGroup + '{"name":"' + group[0].split("\t")[0] + '","sets":[';
			group = group[1];
			
			for (let set of group.split(/\t*?\n\t*?\n\t*?/)) {
				set = set.replace("\n", "\n\n");
				set = set.split("\n\n");
				newGroup = newGroup + '{"name":"' + set[0].split("\t")[0] + '","terms":[';
				set = set[1];
				
				for (let term of set.split("\n")) {
					term = term.split("\t");
					if (term[1])
						newGroup = newGroup + '{"term":"' + term[0] + '","def":"' + term[1] + '","progress":' + (term[2] || 0) + '},';
				}
				if (newGroup.slice(-1) == ",") newGroup = newGroup.slice(0,-1);
				newGroup = newGroup + ']},';
			}
			if (newGroup.slice(-1) == ",") newGroup = newGroup.slice(0,-1);
			newGroup = newGroup + ']}';
			
			let savedGroups = getVar("savedGroups");
			let comma = savedGroups.length != 0 ? ", " : "";
			setVar("groups", (savedGroups + comma + newGroup).replace(/'/g, "’"));
			setVar("savedGroups", getVar("groups"));
			save();
			
			location.reload();
		}
	</script>
	
	<!-- Exporting a group -->
	<script>
		function ExportGroup() {
			document.getElementById("mainMenue").style.visibility = 'hidden';
			
			loadOldElementState("exportGroup");
			document.getElementById("exportGroup").style.visibility = 'visible';
			
			let thisInnerHTML = "";
			let groups = JSON.parse("[ " + getVar("savedGroups") + " ]");
			for (let g of groups) {
				let name = g.name;
				g = JSON.stringify(g);
				thisInnerHTML = thisInnerHTML + `<br><button class='exportButton' onclick='exportSave(\`${g}\`, "${name}.grp");'>${name}</button>`;
			}
			
			document.getElementById("exportWithResetProgress").insertAdjacentHTML("afterend", thisInnerHTML);
		}
		
		function ResetProgressOnExport() {
			const exportButtons = document.getElementsByClassName("exportButton");
			for (let expBut of exportButtons)
				expBut.setAttribute("onclick", expBut.getAttribute("onclick").replace(/\\"progress\\": ?\d+(\.\d+)?/g, '\\"progress\\":0'));
			document.getElementById("exportWithResetProgress").innerHTML = "Progress reset.  To export with progress, reload the page.  ";
			document.getElementById("exportWithResetProgress").style.color = 'red';
		}
		
		function FinishExport() { location.reload(); }
	</script>
	
	<!-- Exporting a group to text -->
	<script>
		function ExportToText() {
			document.getElementById("mainMenue").style.visibility = 'hidden';
			
			loadOldElementState("exportToText");
			document.getElementById("exportToText").style.visibility = 'visible';
			
			let thisInnerHTML = "";
			let groups = JSON.parse("[ " + getVar("savedGroups") + " ]");
			for (let g of groups) {
				let name = g.name;
				g = JSON.stringify(g);
				thisInnerHTML = thisInnerHTML + `<br><button class='exportButton' onclick='GroupToExportToText(\`${g}\`);'>${name}</button>`;
			}
			
			document.getElementById("exportToTextWithResetProgress").insertAdjacentHTML("afterend", thisInnerHTML);
		}
		
		function ResetProgressOnExportToText() {
			const exportButtons = document.getElementsByClassName("exportButton");
			for (let expBut of exportButtons)
				expBut.setAttribute("onclick", expBut.getAttribute("onclick").replace(/\\"progress\\": ?\d+(\.\d+)?/g, '\\"progress\\":0'));
			document.getElementById("exportToTextWithResetProgress").innerHTML = "Progress reset.  To export with progress, reload the page.  ";
			document.getElementById("exportToTextWithResetProgress").style.color = 'red';
		}
		
		function GroupToExportToText(group) {
			group = JSON.parse(group);
			let exportedGroup = `<tr><td>${group.name}</td></tr>`;
			for (let set of group.sets) {
				exportedGroup = exportedGroup + `<tr><td>${set.name}</td></tr>`;
				for (let term of set.terms)
					exportedGroup = exportedGroup + `<tr><td>${term.term}</td><td>${term.def + (term.progress!=undefined ? (`</td><td>${term.progress}`) : ``)}</td></tr>`;
				exportedGroup = exportedGroup + `<tr><td><br></td></tr>`;
			}
			document.getElementById("exportedToText").getElementsByTagName(`table`)[0].innerHTML = exportedGroup;
			
			document.getElementById("exportToText").style.visibility = 'hidden';
			document.getElementById("exportedToText").style.visibility = 'visible';
		}
		
		function FinishExportToText() { location.reload(); }
	</script>
	
	<!-- Importing a group -->
	<script>
		function ImportGroup() {
			document.getElementById("mainMenue").style.visibility = 'hidden';
			
			const importButton = document.getElementById("importButton");
			importButton.addEventListener('change', () => {
				let files = importButton.files;
				if (files.length == 0) return;
				const file = files[0];
				let reader = new FileReader();
				
				reader.onload = (e) => {
					const file = e.target.result;
					const lines = file.split(/\r\n|\n/);
					
					let savedGroups = getVar("savedGroups");
					let comma = savedGroups.length != 0 ? ", " : "";
					setVar("groups", (savedGroups + comma + lines.join("\n")).replace(/'/g, "’"));
					save();
				};
				
				reader.onerror = (e) => alert(e.target.error.name);
				
				reader.readAsText(file);
			});
			
			document.getElementById("importGroup").style.visibility = 'visible';
		}
		
		function FinishImport() {
			setVar("savedGroups", getVar("groups"));
			
			location.reload();
		}
	</script>
	
	<!-- Importing a group from text -->
	<script>
		function ImportFromText() {
			document.getElementById("mainMenue").style.visibility = 'hidden';
			document.getElementById("importFromText").getElementsByTagName("textarea")[0].value = "Group Name\t\nSet Name\t\nTerm\tDefinition\nTerm\tDefinition\nTerm\tDefinition\nTerm\tDefinition\n\t\nSet Name\t\nTerm\tDefinition\nTerm\tDefinition\nTerm\tDefinition\nTerm\tDefinition\nTerm\tDefinition\nTerm\tDefinition";
			document.getElementById("importFromText").style.visibility = 'visible';
		}
		
		function FinishTextImport() {
			let newGroup = '';
			
			let group = document.getElementById("importFromText").getElementsByTagName("textarea")[0].value.replace(/\t\n/g, "\n");
			group = group.replace("\n", "\n\n\n");
			group = group.split("\n\n\n");
			newGroup = newGroup + '{"name":"' + group[0].split("\t")[0] + '","sets":[';
			group = group[1];
			
			for (let set of group.split(/\t*?\n\t*?\n\t*?/)) {
				set = set.replace("\n", "\n\n");
				set = set.split("\n\n");
				newGroup = newGroup + '{"name":"' + set[0].split("\t")[0] + '","terms":[';
				set = set[1];
				
				for (let term of set.split("\n")) {
					term = term.split("\t");
					if (term[1])
						newGroup = newGroup + '{"term":"' + term[0] + '","def":"' + term[1] + '","progress":' + (term[2] || 0) + '},';
				}
				if (newGroup.slice(-1) == ",") newGroup = newGroup.slice(0,-1);
				newGroup = newGroup + ']},';
			}
			if (newGroup.slice(-1) == ",") newGroup = newGroup.slice(0,-1);
			newGroup = newGroup + ']}';
			
			let savedGroups = getVar("savedGroups");
			let comma = savedGroups.length != 0 ? ", " : "";
			setVar("groups", (savedGroups + comma + newGroup).replace(/'/g, "’"));
			setVar("savedGroups", getVar("groups"));
			save();
			
			location.reload();
		}
	</script>
	
	<!-- Deleting a group -->
	<script>
		function DeleteGroup() {
			document.getElementById("mainMenue").style.visibility = 'hidden';
			
			loadOldElementState("deleteGroup");
			document.getElementById("deleteGroup").style.visibility = 'visible';
			
			let thisInnerHTML = "";
			let groups = JSON.parse("[ " + getVar("savedGroups") + " ]");
			for (let i = 0; i < groups.length; i++) {
				let name = groups[i].name;
				let g = JSON.parse(JSON.stringify(groups));
				g.splice(i, 1);
				g = JSON.stringify(g);
				thisInnerHTML = thisInnerHTML + `<br><button id='del${name}' class='deleteButton' onclick='DelGroup(\`${g.slice(1, -1)}\`, "${name}");'>${name}</button>`;
			}
			
			document.getElementById("finishDeleteButton").insertAdjacentHTML("beforebegin", thisInnerHTML);
		}
		
		function DelGroup(group, name) {
			setVar("groups", group);
			setVar("deleteGroupName", name);
			
			for (let elem of document.getElementsByClassName("deleteButton")) {
				elem.style.backgroundColor = null;
			}
			document.getElementById("del" + name).style.backgroundColor = "#ff4444dd";
		}
		
		function FinishDelete() {
			if (confirm("Are you sure you want to delete group \"" + getVar("deleteGroupName") + "\"?  This action cannot be undone.  ")) {
				setVar("savedGroups", getVar("groups"));
				save();
			}
			
			location.reload();
		}
	</script>

	<!-- Logging in and Logging out -->
	<script>
		function Login() {
			document.getElementById("mainMenue").style.visibility = 'hidden';
			
			loadOldElementState("login");
			document.getElementById("login").style.visibility = 'visible';
		}

		async function FinishLogin() {
			let password = document.getElementById("login").getElementsByTagName("input")[0].value;
			await addSecureKey(password);
			location.reload();
		}

		async function Logout() {
			await removeSecureKey();
			localStorage.removeItem("drops-password");
			location.reload();
		}
	</script>

	<h1 id="loading">Loading...</h1>
	
	<div id="mainMenue" style="visibility: hidden; position: absolute; top: 0px; left: 0px;">
		<h1>Choose an Action:</h1>

		<table style="width: 95%; margin: auto; table-layout: fixed">
			<tr>
				<td class="menuTd" colspan="2"><button class="menuButton" onclick="StudyGroup()">Study a Group</button></td>
			</tr>
			<tr>
				<td class="menuTd" colspan="2"><button class="menuButton" onclick="NewGroup()">Create a Group</button></td>
			</tr>
			<tr>
				<td class="menuTd"><button class="menuButton" onclick="EditGroup()" style="color: red; background-color: #ffaaaa">Edit a Group</button></td>
				<td class="menuTd"><button class="menuButton" onclick="EditAsText()">Edit a Group as Text</button></td>
			</tr>
			<tr>
				<td class="menuTd"><button class="menuButton" onclick="ExportGroup()">Export a Group</button></td>
				<td class="menuTd"><button class="menuButton" onclick="ExportToText()">Export a Group to Text</button></td>
			</tr>
			<tr>
				<td class="menuTd"><button class="menuButton" onclick="ImportGroup()">Import a Group</button></td>
				<td class="menuTd"><button class="menuButton" onclick="ImportFromText()">Import a Group from Text</button></td>
			</tr>
			<tr>
				<td class="menuTd" colspan="2"><button class="menuButton" onclick="DeleteGroup()">Delete a Group</button></td>
			</tr>
			<tr>
				<td class="menuTd" id="loginButton" ><button class="menuButton" onclick="Login()" >Log In</button></td>
				<td class="menuTd" id="logoutButton"><button class="menuButton" onclick="Logout()">Log Out</button></td>
			</tr>
		</table>

		<script>startup();</script>
	</div>
	
	
	<div id="studyGroup" style="visibility: hidden; position: absolute; top: 0px; left: 0px;">
		<h1>Choose a group to study:</h1>
		
		<div id="studyWindow" style="height: 80%;">
		</div>
		
		<button id="finishStudyButton" onclick="FinishStudy()" style="color: blue">Finish Studying</button>
		
		<script> saveElementState("studyGroup"); </script>
	</div>
	
	
	<div id="newGroup" style="visibility: hidden; position: absolute; top: 0px; left: 0px;">
		<h1>Enter the name for the new group:</h1>
		
		<input type="text" id="newGroupName">
		
		<button onclick="NewSet()">Create Group</button>
	</div>
	
	<div id="newSet" style="visibility: hidden; position: absolute; top: 0px; left: 0px;">
		<h1>Enter the name for the new set:</h1>
		
		<input type="text" id="newSetName">
		
		<button onclick="NewTerms()">Create Set</button>
		<br>
		<button onclick="FinishGroup()" style="color: blue">Finish Group</button>
	</div>
	
	<table id="newTerms" style="visibility: hidden; position: absolute; top: 0px; left: 0px;">
		<tr>
			<th style="width: 50%"><h1>Term:</h1></th>
			<th style="width: 50%"><h1>Definition:</h1></th>
		</tr>
		
		<tr class="termPair">
			<td><input type="text" class="term" id="currentTerm"></td>
			<td><input type="text" class="def"></td>
		</tr>
		
		<tr id="newTermButtonRow">
			<td colspan="2"><button onclick="NewTerm()">Next Term</button></td>
		</tr>
		
		<tr>
			<td colspan="2"><button onclick="FinishSet()">Finish Set</button></td>
		</tr>
		
		<script> saveElementState("newTerms"); </script>
	</table>
	
	
	<div id="editAsText" style="visibility: hidden; position: absolute; top: 0px; left: 0px;">
		<h1>Choose a group to edit:</h1>
		
		<script> saveElementState("editAsText"); </script>
	</div>
	
	<div id="editingAsText" style="visibility: hidden; position: absolute; top: 0px; left: 0px;">
		<h1>Edit a Group as Text:</h1>

		<div style="width: 95%; margin: auto;">
			<h3 style="text-align: left;">Copy terms from EXCEL or similar program.  </h3>
			<h5 style="text-align: left;">Be sure that columns are separated by tabs and rows by retuns.  </h5>
			<h5 style="text-align: left;">Add the name of the group as the first line.  </h5>
			<h5 style="text-align: left;">Add the name of each set before its terms.  </h5>
			<h5 style="text-align: left;">Leave an additional new line after each set (before the next set's name).  </h5>
		
			<textarea>
			</textarea>
		</div>
		
		<button onclick="FinishEditAsText()" style="color: blue">Finish Edit</button>
	</div>
	
	
	<div id="exportGroup" style="visibility: hidden; position: absolute; top: 0px; left: 0px;">
		<h1>Choose a group to export:</h1>
		<h4>If the download fails, try again and change the file extention to .txt</h4>
		
		<button id="exportWithResetProgress" onclick="ResetProgressOnExport()" style="color: green;">Reset the progress in the export</button>
		
		<br>
		<button onclick="FinishExport();" style="color: blue">Finish Export</button>
		
		<script> saveElementState("exportGroup"); </script>
	</div>
	
	
	<div id="exportToText" style="visibility: hidden; position: absolute; top: 0px; left: 0px;">
		<h1>Choose a group to export:</h1>
		
		<button id="exportToTextWithResetProgress" onclick="ResetProgressOnExportToText()" style="color: green;">Reset the progress in the export</button>
		
		<script> saveElementState("exportToText"); </script>
	</div>
	
	<div id="exportedToText" style="visibility: hidden; position: absolute; top: 0px; left: 0px;">
		<h1>You can copy and paste this into an EXCEL spreadsheet.</h1>
		
		<table></table>
		
		<button onclick="FinishExportToText();" style="color: blue">Finish Export</button>
		
		<script> saveElementState("exportedToText"); </script>
	</div>
	
	
	<div id="importGroup" style="visibility: hidden; position: absolute; top: 0px; left: 0px;">
		<h1>Import a Group:</h1>
		
		<label for="importButton">Select a file:</label>
		<input type="file" id="importButton" name="importButton">
		
		<button onclick="FinishImport()" style="color: blue">Finish Import</button>
	</div>
	
	
	<div id="importFromText" style="visibility: hidden; position: absolute; top: 0px; left: 0px; height: 100%">
		<h1>Import a Group from Text:</h1>

		<div style="width: 95%; margin: auto;">
			<h3 style="text-align: left;">Copy terms from EXCEL or similar program.  </h3>
			<h5 style="text-align: left;">Be sure that columns are separated by tabs and rows by retuns.  </h5>
			<h5 style="text-align: left;">Add the name of the group as the first line.  </h5>
			<h5 style="text-align: left;">Add the name of each set before its terms.  </h5>
			<h5 style="text-align: left;">Leave an additional new line after each set (before the next set's name).  </h5>
		
			<textarea>
			</textarea>
		</div>
		
		<button onclick="FinishTextImport()" style="color: blue">Finish Import</button>
	</div>
	
	
	<div id="deleteGroup" style="visibility: hidden; position: absolute; top: 0px; left: 0px;">
		<h1>Choose a group to delete:</h1>
		<h1>THIS ACTION CANNOT BE UNDONE</h1>
		
		<button onclick="FinishDelete()" id="finishDeleteButton" style="color: blue">Finish Delete</button>
		
		<script> saveElementState("deleteGroup"); </script>
	</div>

	
	<div id="login" style="visibility: hidden; position: absolute; top: 0px; left: 0px;">
		<h1>Password:</h1>
		<input type="password">
		<button onclick="FinishLogin();">Log In</button>
		
		<script> saveElementState("login"); </script>
	</div>
	
</body>
</html>

<?php
	if(isset($_POST["data"])) {
		$authorised = false;
		$keyHashFile = fopen("keys.hash", "r") or die("Unable to open file!");
		$keyFileHashList = explode("\n", fread($keyHashFile,filesize("keys.hash")));
		$key = $_POST["key"];
		$keyHash = hash("sha256", "$key");
		foreach ($keyFileHashList as $keyFileHash) {
			$authorised = ($keyHash == $keyFileHash);
			if($authorised) {
				$datafile = fopen("data.json", "w") or die("Unable to open file!");
				$data = $_POST["data"];
				fwrite($datafile, $data);
				fclose($datafile);
				break;
			}
			else { echo "haha"; }
		}
		fclose($keyHashFile);
	}

	if(isset($_POST["pswdCheck"])) {
		$keyHashFile = fopen("keys.hash", "a") or die("Unable to open file!");
		$newKey = hash("sha256", sprintf("%d%d%d%d", rand(), rand(), rand(), rand()));
		$newKeyHash = hash("sha256", $newKey);
		if(file_exists("password.hash")) {
			$pswdfile = fopen("password.hash", "r") or die("Unable to open file!");
			$pswdfilehash = fread($pswdfile,filesize("password.hash"));
			$pswd = $_POST["pswdCheck"];
			$pswdhash = hash("sha256", "$pswd\n");
			if ("$pswdhash  -\n" == "$pswdfilehash") {
				fwrite($keyHashFile, "$newKeyHash\n");
				echo $newKey;
			}
			else { echo "haha"; }
			fclose($pswdfile);
		}
		else { echo "haha"; }
		fclose($keyHashFile);
	}

	if(isset($_POST["rmKey"])) {
		$keyHashFile = fopen("keys.hash", "r") or die("Unable to open file!");
		$keyFileHashList = explode("\n", fread($keyHashFile,filesize("keys.hash")));
		$key = $_POST["rmKey"];
		$keyHash = hash("sha256", $key);
		unset($keyFileHashList[array_search($keyHash, $keyFileHashList)]);
		fclose($keyHashFile);
		$keyHashFile = fopen("keys.hash", "w") or die("Unable to open file!");
		fwrite($keyHashFile, "");
		fclose($keyHashFile);
		$keyHashFile = fopen("keys.hash", "a") or die("Unable to open file!");
		foreach ($keyFileHashList as $keyFileHash) {
			fwrite($keyHashFile, $keyFileHash);
		}
		fclose($keyHashFile);
	}
		
	// function console_echo($str) {
	//	 $csl = fopen("console.log", "a") or die("Unable to open file!");
	//	 fwrite($csl, "$str\n");
	//	 fclose($csl);
	// }
?>

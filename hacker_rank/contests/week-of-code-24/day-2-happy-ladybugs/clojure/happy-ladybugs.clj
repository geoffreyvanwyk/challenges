(use '[clojure.string :only (split trim)])

(let [game-count (Integer/parseInt (read-line))]
  (defn single-ladybugs?
    [colors ladybugs]
    (some #(= 1 (count %))
          (vals (group-by #(colors %)
                          ladybugs))))

  (defn first-happy?
    [ladybugs]
    (= (first ladybugs)
       (first (rest ladybugs))))

  (loop [counter game-count
         cells   []
         games   []]
    (if (> counter 0)
      (recur (dec counter)
             (conj cells (Integer/parseInt (read-line)))
             (conj games (read-line)))
      (loop [[c & cs] cells
             [g & gs] games]
        (let [ladybugs   (filter #(not= % "_") (split g #""))
              colors     (set ladybugs)]
         (cond
          (zero? (count ladybugs))           (println "YES")
          (single-ladybugs? colors ladybugs) (println "NO")
          (> c (count ladybugs))             (println "YES")
          (first-happy? ladybugs)            (println "YES")
          :else                              (println "NO"))
        (if (not (empty? cs))
         (recur cs gs)))))))
